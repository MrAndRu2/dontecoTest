<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "worker".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $job_titles
 * @property string|null $department
 * @property string|null $full_or_part_time
 * @property string|null $salary_or_hourly
 * @property int|null $typical_hours
 * @property float|null $annual_salary
 * @property float|null $hourly_rate
 * @property string $date_created
 */
class Worker extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'worker';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'job_titles', 'department', 'full_or_part_time', 'salary_or_hourly'], 'string'],
            [['typical_hours'], 'integer'],
            [['annual_salary', 'hourly_rate'], 'number'],
            [['date_created'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'job_titles' => 'Job Titles',
            'department' => 'Department',
            'full_or_part_time' => 'Full Or Part Time',
            'salary_or_hourly' => 'Salary Or Hourly',
            'typical_hours' => 'Typical Hours',
            'annual_salary' => 'Annual Salary',
            'hourly_rate' => 'Hourly Rate',
            'date_created' => 'Date Created',
        ];
    }
}
