<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    /**
     * Download backup database dalam format SQL 1-Klik
     */
    public function download(): StreamedResponse|Response
    {
        $filename = 'feedmart_backup_'.date('Ymd_His').'.sql';

        try {
            $tables = DB::select('SHOW TABLES');
            $dbName = config('database.connections.mysql.database');
            $tableKey = "Tables_in_{$dbName}";

            $response = new StreamedResponse(function () use ($tables) {
                $handle = fopen('php://output', 'w');

                fwrite($handle, "-- ==========================================================\n");
                fwrite($handle, "-- FEEDMART POS SYSTEM DATABASE BACKUP\n");
                fwrite($handle, '-- Tanggal Backup: '.date('Y-m-d H:i:s')."\n");
                fwrite($handle, "-- ==========================================================\n\n");
                fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

                foreach ($tables as $tableObj) {
                    $tableArray = (array) $tableObj;
                    $tableName = reset($tableArray);

                    // Skip migration & job internal tables if desired or include all
                    fwrite($handle, "-- ---------------------------------------------------------\n");
                    fwrite($handle, "-- Struktur tabel `{$tableName}`\n");
                    fwrite($handle, "-- ---------------------------------------------------------\n");
                    fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");

                    $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                    $createArray = (array) $createTable[0];
                    $createSql = $createArray['Create Table'] ?? reset($createArray);
                    fwrite($handle, $createSql.";\n\n");

                    // Dump Data
                    $rows = DB::table($tableName)->get();
                    if ($rows->count() > 0) {
                        fwrite($handle, "-- Data untuk tabel `{$tableName}`\n");
                        foreach ($rows as $row) {
                            $rowArray = (array) $row;
                            $fields = array_map(function ($val) {
                                if (is_null($val)) {
                                    return 'NULL';
                                }

                                return "'".addslashes((string) $val)."'";
                            }, array_values($rowArray));

                            $columns = array_map(fn ($col) => "`{$col}`", array_keys($rowArray));
                            $insertSql = "INSERT INTO `{$tableName}` (".implode(', ', $columns).') VALUES ('.implode(', ', $fields).");\n";
                            fwrite($handle, $insertSql);
                        }
                        fwrite($handle, "\n");
                    }
                }

                fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
                fwrite($handle, "-- SELESAI BACKUP FEEDMART --\n");
                fclose($handle);
            });

            $response->headers->set('Content-Type', 'application/sql');
            $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

            return $response;
        } catch (Exception $e) {
            return back()->with('error', 'Gagal membuat backup database: '.$e->getMessage());
        }
    }
}
