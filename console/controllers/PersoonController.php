<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use Faker\Factory;

class PersoonController extends Controller
{
    /**
     * Seed the `persoon` table with fake data
     * @param int $count Number of rows to insert
     */
    public function actionSeed($count = 100)
    {
        $faker = Factory::create('nl_NL'); // Dutch locale for names, etc.

        for ($i = 0; $i < $count; $i++) {
            $createdBy = 1;
            $updatedBy = 1;

            $rows[] = [
                // regnr: Random alphanumeric string (like a registry number)
                strtoupper($faker->bothify('??#####')),

                // naam: Last name
                $faker->lastName(),

                // voornaam: First name
                $faker->firstName(),

                // idnr: Some random numeric ID
                $faker->numerify('#########'),

                // verzekeringskaartnr: Insurance card number
                strtoupper($faker->bothify('??#####??')),

                // geboortedatum: Random date between 18–80 years ago
                $faker->date('Y-m-d', '-18 years'),

                // created_by
                $createdBy,

                // updated_by
                $updatedBy,

                // created_at
                date('Y-m-d H:i:s'),

                // updated_at
                date('Y-m-d H:i:s'),
            ];
        }

        Yii::$app->db->createCommand()
            ->batchInsert('persoon', [
                'regnr',
                'naam',
                'voornaam',
                'idnr',
                'verzekeringskaartnr',
                'geboortedatum',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at'
            ], $rows)
            ->execute();

        echo "Inserted {$count} fake `persoon` records.\n";
    }
}
