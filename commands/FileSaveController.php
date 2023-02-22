<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Worker;
use yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Transaction;
use mysqli_sql_exception;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class FileSaveController extends Controller
{

    /**
     * консольная команда для загрузки пользователей из файла в базу данных
     * 
     * @todo при необходимости можно разбить запрос в таблицу на несколько, 
     * если запрос будет выполнятся очень долго. 
     * Однако для 31000 пользователей особой разницы я не заметил. Вероятно можно потестить если файл будет на 1000000 пользователей
     * 
     * @param string $path - полный путь к файлу
     * @return void
     */
    public function actionSetUsers($path)
    {
        $stream = fopen($path, 'r');
        if (!$stream) {
            exit('Ошибка загрузки файла');
        }
        /** проходим весь файл, формируя строку для запроса*/
        $insertValues = '';
        while ($data = fgetcsv($stream)) {
            /** если строка будет для названий столбцов, пропустим*/
            if ($data[0] == 'Name') {
                continue;
            }
            /**оформляем данные так, как нужно бд */
            $data = self::checkUserData($data);
            /** соединяем данные для запроса INSERT в бд*/
            $insertValues .= '(' . implode(',', $data) . '),';
        }
        /**удаляем последнюю запятую */
        $insertValues = substr($insertValues, 0, -1);
        /**запрос в бд */
        self::uploadUsers($insertValues);
    }

    /**
     * проверка данных пользователя
     * функция оформляет данные для запроса INSERT, расставляя кавычки где нужно или null где данных нет
     * 
     * @param array $data - массив данных по конкретному пользователю
     * @return array $data - офромленные данные
     */
    private function checkUserData(array $data): array
    {
        foreach ($data as &$param) {
            switch (true) {
                case empty($param):
                    $param = 'NUll';
                    break;
                case is_numeric($param):
                    $param = $param;
                    break;
                case is_string($param):
                    $param = "\"$param\"";
                    break;
            }
        }
        return $data;
    }

    /**
     * загрузка пользователей в бд
     * загружает всех пользователей из файла в бд. При ошибке откатывает запрос
     * @param string $insertValues - оформленные данные для INSERT запроса пример '("name", "job_titles"...., hourly_rate), (...)'
     * @return void
     */
    private function uploadUsers(string $insertValues): void
    {
        $db = mysqli_connect(
            Yii::$app->params['db_hostname'],
            Yii::$app->params['db_username'],
            Yii::$app->params['db_password'],
            Yii::$app->params['db_name']
        );

        $table = Worker::tableName();

        $command = "INSERT INTO $table 
        ( name, job_titles, department, full_or_part_time, salary_or_hourly, typical_hours, annual_salary, hourly_rate) 
        VALUES $insertValues";

        mysqli_begin_transaction($db);
        try {
            mysqli_query($db, $command);
            mysqli_commit($db);
            mysqli_close($db);
            echo 'Данные загружены';
        } catch (mysqli_sql_exception $exception) {
            mysqli_rollback($db);
            mysqli_close($db);
            throw $exception;
        }
    }
}
