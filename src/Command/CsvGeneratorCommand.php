<?php

// src/Command/CreateUserCommand.php
namespace App\Command;

use Faker\Factory;
use Faker\Generator;
use SplFileObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CsvGeneratorCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:csv-generator';

    private string $projectDir;

    private Generator $faker;

    private array $repositoryFaker = [
        'reference' => [],
        'code' => [],
    ];

    /**
     * CsvGeneratorCommand constructor.
     */
    public function __construct($projectDir)
    {
        parent::__construct();

        $this->projectDir = $projectDir;
        $this->faker = Factory::create('fr_FR');
    }

    protected function configure(): void
    {
        $this->setDescription('Creates a supplier CSV')
            ->setHelp('This command allows you to create a csv to test your own import code');

        $this->addArgument('total', InputArgument::REQUIRED, 'Number row to generate');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $total = (int)$input->getArgument('total');

        if ($total > 1000000) {
            $total = 1000000;
        }

        $output->writeln('total row to generate : ' . $total);

        $csv = $this->generateCsv();

        $csv->fputcsv($this->getHeader(), ";");

        $i = 1;
        while ($i <= $total) {
            $output->write("\rcreate row " . $i);
            $row = $this->generateRow();
            $csv->fputcsv($row, ";");
            $i++;
        }

        $output->writeln("");
        $output->writeln("file generate at " . $csv->getRealPath());

        return Command::SUCCESS;
    }

    /**
     * @return SplFileObject
     */
    private function generateCsv(): SplFileObject
    {
        $fileName = $this->projectDir . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'generate';
        if (!is_dir($fileName)) {
            mkdir($fileName);
        }

        $fileName .= DIRECTORY_SEPARATOR . 'infinity_energy_test_' . date('YmdHis') . '.csv';

        return new SplFileObject($fileName, 'w');
    }

    /**
     * @return string[]
     */
    private function getHeader(): array
    {
        return [
            'Immatriculation',
            //| Format FR depuis 2009 | AA-666-BB               | vehicle (plate_number) |
            'Marque',
            //| Libre                 | Peugeot                 | vehicle (brand) |
            'Model',
            //| Libre                 | 208                     | vehicle (model) |
            'Catégorie  de dépense',
            //| Enum : gasoline,diesel,electricity_charge,gpl,hydrogen |  gasoline  | expense (category) |
            'Libellé',
            //| Libre                 | Prise de carburant      | expense (description) |
            'HT',
            //| Decimal(10,3) FR      | 10,516                  | expense (value_te) |
            'TTC',
            //| Decimal(10,3) FR      | 10,516                  | expense (value_ti) |
            'TVA',
            //| Decimal(5,3) FR       | 20,000                  | expense (tax_rate) |
            'Date & heure',
            //| Format datetime FR    | 01/12/2018 10:59:59     | expense (issued_on) |
            'Numéro facture',
            //| Libre (unique)        | FAC000000000001         | expense (invoice_number) |
            'Code dépense',
            //| Libre (unique)        | DEP000000000001         | expense (expense_number) |
            'Station',
            //| Libre                 | INFINITY ACCESS, Chemin d'Innovation, 04 06 04 06 04 | gas_station (description) |
            'Position GPS (Latitude) ',
            //,| Coordonnée GPS        | 40.71727401             | gas_station (coordinate) |
            'Position GPS (Longitude)',
            //| Coordonnée GPS        | -74.00898606            | gas_station (coordinate) |
        ];
    }

    /**
     * @return array
     */
    private function generateRow(): array
    {
        $vehicule = $this->generateVehicule();
        $expense = $this->generateExpense();
        $station = $this->generateStation();

        return [
            $this->generatePlateNumber(),
            $vehicule['brand'],
            $vehicule['model'],
            $this->generateExpenseCategory(),
            "Prise de carburant",
            $expense['te'],
            $expense['ti'],
            $expense['tax'],
            $this->generateIssuedOn(),
            $this->generateReference(),
            $this->generateCode(),
            $station['description'],
            $station['latitude'],
            $station['longitude'],
        ];
    }

    /**
     * @return string
     */
    private function generatePlateNumber(): string
    {
        return strtoupper($this->faker->bothify('??-###-??'));
    }

    /**
     * @return string
     */
    private function generateExpenseCategory(): string
    {
        return $this->faker->randomElement(['gasoline', 'diesel', 'electricity_charge', 'gpl', 'hydrogen']);
    }

    /**
     * @return array
     */
    private function generateExpense(): array
    {
        $expenseTe = $this->faker->randomFloat(3);
        $expenseTi = round($expenseTe * 1.2, 3);

        return [
            'te' => str_replace('.', ',', (string)$expenseTe),
            'ti' => str_replace('.', ',', (string)$expenseTi),
            'tax' => str_replace('.', ',', (string)20.000)
        ];
    }

    /**
     * @return array
     */
    private function generateVehicule(): array
    {
        $vehicule = [
            'peugeot' => [
                'Expert',
                'Boxer',
                '308 Affaire',
                '308 Affaire SW',
                'Boxer Plateau Ridelles SC',
                '5008',
                '3008',
                'Rifter',
                '208',
                '208 Affaire',
                '2008',
                '308 SW',
                '308',
                '108',
                '508',
                'Partner',
                'Traveller',
                'Boxer Chassis Cabine SC',
                '508 SW',
                'e-208',
                'e-2008',
                'Boxer Chassis Benne',
            ],
            'renault' => [
                'Trafic',
                'Master SC',
                'Mégane Estate',
                'Clio',
                'Kangoo Express',
                'Master',
                'Captur',
                'Kangoo',
                'Mégane Berline',
                'Mégane Société',
                'Koléos',
                'Scénic',
                'Grand Scénic',
                'ZOE',
                'Kadjar',
                'Master DC',
                'Espace',
                'Talisman',
                'Talisman Estate',
                'Trafic Navette',
                'Clio Société',
                'Twingo',
                'Arkana',
                'Express',
            ],
            'volkswagen' => [
                'Sharan',
                'Multivan',
                'Touran',
                'California',
                'Polo',
                'Caravelle',
                'T-Roc',
                'Passat',
                'Transporter',
                'Crafter',
                'Up 2.0',
                'Passat SW',
                'Tiguan',
                'Transporter ProCab',
                'Golf',
                'Golf SW',
                'Arteon',
                'Tiguan Allspace',
                'e-Crafter',
                'T-cross',
                'Crafter Benne',
                'Grand California',
                'ID.3',
                'Crafter Plateau',
                'Arteon Shooting Brake',
                'Caddy',
                'Caddy Cargo',
                'Touareg',
                'ID.4'
            ],
            'bmw' => [
                'X5',
                'X3',
                'Série 1',
                'Série 5',
                'Série 6 Gran Turismo',
                'Série 5 Touring',
                'X4',
                'X6',
                'X1',
                'Série 3 Touring',
                'Série 4',
                'Série 3 Berline',
                'Série 4 Cabriolet',
                'Série 2 Coupé',
                'Série 8 Coupé',
                'Série 4 Gran Coupé',
                'Série 2 Active Tourer',
                'Série 2 Cabriolet',
                'i8 Coupé',
                'Série 2 Gran Tourer',
                'i3',
                'i3s',
                'X2',
                'i8 Roadster',
                'M5',
                'Z4',
                'X7',
                'Série 8 Cabriolet',
                'Série 8 Gran Coupé',
                'Série 2 Gran Coupé',
                'iX3',
                'iX',
                'Série 7 Berline',
                'Série 7 Limousine',
                'i4',
            ],
            'mercedes' => [
                'Sprinter SC',
                'Sprinter DC',
                'Sprinter',
                'Vito Tourer Compact',
                'Classe E Berline',
                'Classe E Break',
                'Classe A Compact',
                'Classe V Long',
                'Vito Compact',
                'Vito Long',
                'Vito Extra Long',
                'Vito Tourer Extra Long',
                'Vito Tourer Long',
                'GLC',
                'Classe G',
                'Citan',
                'CLA Coupe',
                'Classe E Cabriolet',
                'Classe E Coupe',
                'GLA',
                'Classe C Coupe',
                'Classe C Cabriolet',
                'Vito Mixto Long',
                'Vito Mixto Extra Long',
                'Vito Mixto Compact',
                'Classe V Extra-Long',
                'Classe V Compact',
                'CLA Shooting Brake',
                'Classe S Limousine',
                'AMG GT',
                'GLC Coupe',
                'Marco Polo',
                'AMG GT Roadster',
                'Classe S Berline',
                'CLS Coupe',
                'Classe C Break',
                'Classe C Berline',
                'Classe A Berline',
                'EQC',
                'GLS',
                'Classe B',
                'GLB',
                'GLE',
                'GLE Coupe',
                'EQV',
                'EQA'
            ],
        ];

        $brand = $this->faker->randomElement(array_keys($vehicule));
        $model = $this->faker->randomElement($vehicule[$brand]);

        return [
            'brand' => strtoupper($brand),
            'model' => $model,
        ];
    }

    /**
     * @return string|null
     */
    private function generateIssuedOn(): ?string
    {
        $date = $this->faker->dateTimeBetween('-1 month');

        $addMistake = rand(0, 100) === 0;

        if ($addMistake) {
            return $this->faker->randomElement(['0000-00-00 00:00:00', '9999-99-99 99:99:99', null]);
        }

        return $date->format('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    private function generateReference(): string
    {
        $reference = $this->faker->bothify('REF-?????????-#########');

        if (in_array($reference, $this->repositoryFaker['reference'])) {
            $reference = $this->generateReference();
        }

        return strtoupper($reference);
    }

    /**
     * @return string
     */
    private function generateCode(): string
    {
        $reference = $this->faker->bothify('CODE-?????????-#########');

        if (in_array($reference, $this->repositoryFaker['code'])) {
            $reference = $this->generateCode();
        }

        return strtoupper($reference);
    }

    /**
     * @return array
     */
    private function generateStation(): array
    {
        return [
            'description' => str_replace("\n", ' ', 'INFINITY ACCESS, ' . $this->faker->address()),
            'longitude' => $this->faker->longitude(),
            'latitude' => $this->faker->latitude()
        ];
    }
}