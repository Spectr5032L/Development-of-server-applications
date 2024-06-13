<?php

namespace App\Http\Controllers;

use App\Http\DTO\ChangeLogsDTO;
use App\Models\ChangeLogs;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ChangeLogsController extends Controller
{
    public static function create(ChangeLogsDTO $changeLogsDTO)
    {
        DB::beginTransaction();

        try {
            $changeLog = new ChangeLogs($changeLogsDTO->toArray());
            $changeLog->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка создания лога изменений'], 500);
        }
    }

    public static function getStory($entity, $id)
    {
        DB::beginTransaction();

        try {
            $changeLogs = ChangeLogs::where('entity', $entity)->where('record', $id)->get();
            $changeLogs->transform(function ($log) {
                $oldRecord = json_decode($log->old_record, true) ?? [];
                $newRecord = json_decode($log->new_record, true) ?? [];

                $changedFields = [];

                foreach ($newRecord as $key => $newValue) {
                    $oldValue = $oldRecord[$key] ?? null;
                    if ($newValue !== $oldValue && $key !== 'updated_at') {
                        $changedFields[$key] = [
                            'old_value' => $oldValue,
                            'new_value' => $newValue,
                        ];
                    }
                }
                $log->old_record = array_intersect_key($oldRecord, $changedFields);
                $log->new_record = array_intersect_key($newRecord, $changedFields);
                return $log;
            });

            DB::commit();

            return $changeLogs;
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка получения истории изменений'], 500);
        }
    }

    public static function change($record_id, $log_id)
    {
        DB::beginTransaction();

        try {
            $log = ChangeLogs::find($log_id);
            if (!$log)
            {
                return ['error' => 'Запись в таблице change_logs с таким id не найдена'];

            }
            $entity = $log->entity;
            $modelClass = "App\\Models\\" . ucfirst(substr($entity, 0, -1));

            $record = $modelClass::find($record_id);

            $oldData = json_decode($log->old_record, true);
            $oldDataForLogs = $record->toArray();

            if ($record)
            {
                if ($oldData == null)
                {
                    $record->forceDelete();
                    $newDataLogs = null;
                    DB::commit();
                }
                else
                {
                    $record->update($oldData);
                    $newDataLogs = $record->toArray();
                    DB::commit();
                }

                
                $changeLogsDTO = new ChangeLogsDTO(
                    $entity,
                    $record->id,
                    json_encode($oldDataForLogs),
                    json_encode($newDataLogs),
                    auth()->id()
                );
                ChangeLogsController::create($changeLogsDTO);
            }
            else
            {
                return ['error' => 'Запись в таблице ' . $entity . ' с таким id не найдена'];
            }


            return ['Обновлённая запись' => $record];
        } catch (Exception $e) {
            DB::rollback();

            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка обновления записи'], 500);
        }
    }
}