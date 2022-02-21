<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\GasStation;
use App\Entity\Vehicle;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use DateTime;
use SplFileObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ImportController extends AbstractController
{
    /**
     * @Route("/importcsvfile", methods="POST", name="app_dash_importfile")
     */
    public function importCSV(Request $request): RedirectResponse
    {
        // get the file from the request
        $csvfile = $request->files->get('csvfile');

        // Check if file exceeds the maximum number of lines allowed
        $file = new SplFileObject($csvfile->getPathname(), 'r');
        $file->seek(PHP_INT_MAX);
        if ($file->key() > 1000001){
            $this->addFlash('warning','Votre fichier dépasse le nombre maximum de ligne');
            return $this->redirect("/");
        }

        // import the file
        if ($this->import($csvfile)) {
            $this->addFlash('success','Votre fichier a été importer avec succès');
        } else {
            $this->addFlash('error','Le fichier n\'est pas bon');
        }

        return $this->redirect("/");
    }

    /**
     * Function used to import the csv file
     * @param $csvfile
     * @return bool
     */
    public function import($csvfile): bool
    {
        $em = $this->getDoctrine()->getManager();
        if (($handle = fopen($csvfile->getPathname(), "r")) !== false)
        {
            $count = 0;
            $batchSize = 1000;
            $header = fgetcsv($handle, 0, ";");

            if(!$this->checkFile($header)) {
                return false;
            }

            while (($data = fgetcsv($handle, 0, ";")) !== false)
            {
                if ($this->validRow($data)){
                    $count++;
                    $vehicle = new Vehicle();
                    $gas_station = new GasStation();
                    $expense = new Expense();

                    // vehicle
                    // only create a new vehicle entity if the vehicle does not exist
                    $vehicleCheck = $this->getDoctrine()->getRepository(Vehicle::class)->findOneBy(['plateNumber' => $data[0]]);
                    if (empty($vehicleCheck)){
                        $vehicle->setPlateNumber($data[0]);
                        $vehicle->setBrand($data[1]);
                        $vehicle->setModel($data[2]);
                    } else {
                        $vehicle = $vehicleCheck;
                    }

                    //expense
                    $valueTe = number_format(floatval(str_replace(',','.',$data[5])), 3, '.', '');
                    $valueTi = number_format(floatval(str_replace(',','.',$data[6])), 3, '.', '');

                    $expense->setVehicle($vehicle);
                    $expense->setCategory($data[3]);
                    $expense->setExpenseNumber($data[10]);
                    $expense->setInvoiceNumber($data[9]);
                    $expense->setDescription($data[4]);
                    $expense->setIssuedOn(DateTime::createFromFormat('Y-m-d H:i:s', $data[8]));
                    $expense->setTaxRate($data[7]);
                    $expense->setValueTe($valueTe);
                    $expense->setValueTi($valueTi);

                    //gas station
                    $gas_station->setExpense($expense);
                    $gas_station->setDescription($data[11]);
                    $gas_station->setCoordinate(new Point($data[12], $data[13], null));

                    //persist data to database
                    $em->persist($vehicle);
                    $em->persist($expense);
                    $em->persist($gas_station);

                    if (($count % $batchSize) === 0 )
                    {
                        // commit to database every 1000 insert
                        $em->flush();
                        $em->clear();
                    }
                }
            }
            fclose($handle);
            // commit to database entities left if there are any
            $em->flush();
            $em->clear();
        }
        return true;
    }

    /**
     * Function used to validate a row (line)
     * @param $row
     * @return bool
     */
    public function validRow($row): bool
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $row[8]);
        $valueTe = number_format(floatval(str_replace(',','.',$row[5])), 3, '.', '');
        $valueTi = number_format(floatval(str_replace(',','.',$row[6])), 3, '.', '');

        // expense number must be unique in the expense table
        $expenseCheck = $this->getDoctrine()->getRepository(Expense::class)->findOneBy(['expenseNumber' => $row[10]]);
        if (!empty($expenseCheck)) {
            return false;
        }

        // date must be valid
        if (!$date or !$this->validDate($date)) {
            return false;
        }

        // values must be compatible with database
        if (strlen($valueTe) - 1 > 10 or strlen($valueTi) - 1 > 10) {
            return false;
        }
		
		// values must be logical
        if (floatval($valueTe) < 0 or floatval($valueTi) < 0) {
            return false;
        }

        return true;
    }

    /**
     * Function used to validate a date
     * @param DateTime $date
     * @return bool
     */
    public function validDate(DateTime $date): bool
    {
        $year = (int) $date->format('Y');
        $month = (int) $date->format('m');
        $day = (int) $date->format('d');
        $hour = (int) $date->format('H');
        $minute = (int) $date->format('i');
        $second = (int) $date->format('s');

        if($year <= 0 or $year > 9999){
            return false;
        }
        if($month <= 0 or $month > 12){
            return false;
        }
        if($day <= 0 or $day > 31){
            return false;
        }
        if($hour < 0 or $day > 23){
            return false;
        }
        if($minute < 0 or $minute > 59){
            return false;
        }
        if($second < 0 or $minute > 59){
            return false;
        }
        return true;
    }

    /**
     * Function used to check if the file is correct
     * @param $header
     * @return bool
     */
    private function checkFile($header): bool
    {
        // first check if the file is correct by the number of columns
        if (count($header) != 14) {
            return false;
        }

        // check each column header is in the right position and that it is correct to be certain it's the correct file
        if ($header[0] != "Immatriculation" or
            $header[1] != "Marque" or
            $header[2] != "Model"  or
            $header[3] != "Catégorie  de dépense"  or
            $header[4] != "Libellé"  or
            $header[5] != "HT"  or
            $header[6] != "TTC"  or
            $header[7] != "TVA"  or
            $header[8] != "Date & heure"  or
            $header[9] != "Numéro facture"  or
            $header[10] != "Code dépense"  or
            $header[11] != "Station"  or
            $header[12] != "Position GPS (Latitude) "  or
            $header[13] != "Position GPS (Longitude)") {
            return false;
        }

        return true;
    }
}