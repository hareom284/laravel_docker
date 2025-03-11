<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Src\Company\Document\Application\UseCases\Queries\FindAllRenovationDocumentsQuery;
use Src\Company\Document\Infrastructure\EloquentModels\AOWIndexEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\ItemsIndexEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionsIndexEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationItemsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionAreaOfWorkEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationSectionsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationAreaOfWorkEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplateItemsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;

//Notification Helper to Vue Component
if (!function_exists('getNotifications')) {
    function getNotifications()
    {
        $notification = Cache::remember('unread_notifications_' . auth()->id(), 60, function () {
            return auth()->user()
                ? auth()->user()->unreadNotifications()->with('notifiable')->paginate(7)
                : null;
        });

        $notification = $notification
            ? ["notifications" => $notification, "unread" =>  $notification->total()]
            : null;

        return $notification;
    }
}


//this is global function that check which user has access

if (!function_exists('authorize')) {
    /* @throws UnauthorizedUserException */
    function authorize($ability, $policy, $arguments = []): bool
    {
        if ($policy::{$ability}(...$arguments)) {
            return false;
        }

        return true;
    }
}


/****
 *  get disk space according to disk array
 */

if (!function_exists('getFileSystemWithRole')) {
    function getFileSystemWithRole($userrole)
    {
        try {
            if ($userrole == config('userrole.bcsuperadmin')) {
                return ['local', 'avatars', 'media_user', 'media_organization'];
            } else if ($userrole == config('userrole.organization_admin')) {
                return ['media_students', 'media_teachers'];
            } else if ($userrole == config('userrole.teacher')) {
                return ['media_teachers'];
            } else {
                return ['local', 'avatars', 'media_user', 'media_organization'];
            }
        } catch (\Exception $error) {

            return ['local'];
        }
    }
}



if (!function_exists('deleteOldRecordData')) {
    function deleteOldRecordData($document_id)
    {
        RenovationSectionsEloquentModel::where('document_id', $document_id)->forceDelete();
        RenovationAreaOfWorkEloquentModel::where('document_id', $document_id)->forceDelete();
        RenovationItemsEloquentModel::where('renovation_document_id', $document_id)->forceDelete();
        SectionsEloquentModel::where('document_id', $document_id)->forceDelete();
        SectionAreaOfWorkEloquentModel::where('document_id', $document_id)->forceDelete();
        QuotationTemplateItemsEloquentModel::where('document_id', $document_id)->forceDelete();
        AOWIndexEloquentModel::where('document_id', $document_id)->forceDelete();
        ItemsIndexEloquentModel::where('document_id', $document_id)->forceDelete();
        SectionsIndexEloquentModel::where('document_id', $document_id)->forceDelete();
    }
}

if (!function_exists('generateAgreementNumber')) {
    /**
     * Generate Agreement Number based on format saved in settings table
     * @param $type The type of the agreement number
     * Allowed values are:
     * - "project": project agreement number's format.
     * - "renovation_document": renovation document agreement number's format.
     * - "signed_project": signed project agreement number's format.
     * - "signed_renovation_document": project agreement number's format.
     *
     *  @param $data The project or document data for agreement number
     * Some of the object attributes are (they are optional, but try to pass in a value for all of the attributes):
     * - "company_initial": Company name's initial.
     * - "date": Created date of project or document.
     * - "salesperson_initial": Project salesperson's initial.
     * - "block_num": Project properties' block number.
     * - "document_type": Document's type (e.g. QO, VO, FOC, CN).
     * - "version_number": Document's version.
     * - "running_num": Project's invoice_no column.
     * @author Chen Ming
     */
    function generateAgreementNumber($type, $data)
    {
        if ($type == 'project')
            $setting = 'project_agr_no_format';
        elseif ($type == 'renovation_document')
            $setting = 'renovation_document_agr_no_format';
        elseif ($type == 'signed_project')
            $setting = 'signed_project_agr_no_format';
        elseif ($type == 'signed_renovation_document')
            $setting = 'signed_renovation_document_agr_no_format';
        elseif ($type == 'customer_invoice')
            $setting = 'invoice_no_format';

        $agrNoSetting = GeneralSettingEloquentModel::where('setting', $setting)->first();
        // Default format for those server that do not have these settings set
        if (!isset($agrNoSetting) || (isset($agrNoSetting) && empty($agrNoSetting->value))) {
            if ($type == 'project')
                $formatString = '{company_initial}/{salesperson_initial}/{date:my}/{block_num}';
            elseif ($type == 'renovation_document')
                $formatString = '{project_agr_no}/{document_type}{version_num}';
            elseif ($type == 'signed_project')
                $formatString = '{company_initial}/{salesperson_initial}/{date:my}/{block_num}/{running_num}';
            elseif ($type == 'signed_renovation_document')
                $formatString = '{project_agr_no}/{document_type}{version_num}';
            elseif ($type == 'customer_invoice')
                $formatString = '{invoice_initial}{date:Ym}{invoice_no:2}.{total_customer_payment}';
        } else
            $formatString = $agrNoSetting->value;
        // Format date
        $date = Carbon::createFromFormat('Y-m-d', $data['date'] ?? '');

        while (preg_match('/\{date:([^\}]+)\}/', $formatString, $matches)) {
            $dateFormat = $matches[1];
            $formattedDate = $date->format($dateFormat);
            $formatString = str_replace($matches[0], $formattedDate, $formatString);
        }

        while (preg_match('/\{running_num(?::(\d+))?\}/', $formatString, $matches)) {
            $digitLength = isset($matches[1]) ? (int)$matches[1] : null;
            $runningNum = $data['running_num'] ?? 0;
            $formattedNumber = $digitLength
                ? str_pad($runningNum, $digitLength, '0', STR_PAD_LEFT)
                : $runningNum;

            $formatString = str_replace($matches[0], $formattedNumber, $formatString);
        }

        while (preg_match('/\{common_quotation_running_num(?::(\d+))?\}/', $formatString, $matches)) {
            $digitLength = isset($matches[1]) ? (int)$matches[1] : null;
            $runningNum = $data['common_quotation_running_number'] ?? 0;
            $formattedNumber = $digitLength
                ? str_pad($runningNum, $digitLength, '0', STR_PAD_LEFT)
                : $runningNum;

            $formatString = str_replace($matches[0], $formattedNumber, $formatString);
        }

        while (preg_match('/\{common_project_running_num(?::(\d+))?\}/', $formatString, $matches)) {
            $digitLength = isset($matches[1]) ? (int)$matches[1] : null;
            $runningNum = $data['common_project_running_number'] ?? 0;
            $formattedNumber = $digitLength
                ? str_pad($runningNum, $digitLength, '0', STR_PAD_LEFT)
                : $runningNum;

            $formatString = str_replace($matches[0], $formattedNumber, $formatString);
        }

        while (preg_match('/\{invoice_no(?::(\d+))?\}/', $formatString, $matches)) {
            $digitLength = isset($matches[1]) ? (int)$matches[1] : null;
            $runningNum = $data['invoice_no'] ?? 0;
            $formattedNumber = $digitLength
                ? str_pad($runningNum, $digitLength, '0', STR_PAD_LEFT)
                : $runningNum;

            $formatString = str_replace($matches[0], $formattedNumber, $formatString);
        }

        while (preg_match('/\{invoice_running_num(?::(\d+))?\}/', $formatString, $matches)) {
            $digitLength = isset($matches[1]) ? (int)$matches[1] : null;
            $runningNum = $data['invoice_running_num'] ?? 0;
            $formattedNumber = $digitLength
                ? str_pad($runningNum, $digitLength, '0', STR_PAD_LEFT)
                : $runningNum;

            $formatString = str_replace($matches[0], $formattedNumber, $formatString);
        }

        $block_num = '';

        if($data['block_num']) {
            $block_num = $data['block_num'];
            $block_num = str_replace(array('Blk', 'Blk ', 'Block', 'Block ', 'blk', 'blk ', 'BLK', 'BLK ', 'BLOCK', 'BLOCK '), '', $block_num);
            $block_num = trim($block_num);
        }

        $placeholders = [
            '{company_initial}' => $data['company_initial'] ?? '',
            '{quotation_initial}' => $data['quotation_initial'] ?? '',
            '{salesperson_initial}' => $data['salesperson_initial'] ?? '',
            '{block_num}' => $block_num ?? '',
            '{document_type}' => $data['document_type'] ?? '',
            '{version_num}' => $data['version_num'] ?? '',
            '{running_num}' => $data['running_num'] ?? '',
            '{project_id}' => $data['project_id'] ?? '',
            '{quotation_num}' => $data['quotation_num'] ?? '',
            '{project_agr_no}' => $data['project_agr_no'] ?? '',
            '{common_quotation_running_num}' => $data['common_quotation_running_number'] ?? '',
            '{common_project_running_num}' => $data['common_project_running_number'] ?? '',
            '{common_invoice_running_num}' => isset($data['common_invoice_running_number']) ?? 0,
            '{invoice_initial}' => $data['invoice_initial'] ?? '',
            '{invoice_no}' => $data['invoice_no'] ?? '',
            '{total_customer_payment}' => $data['total_customer_payment'] ?? '',
            '{invoice_running_num}' => $data['invoice_running_num'] ?? ''
        ];
        // Remove placeholders that have null values
        $placeholders = array_filter($placeholders, function ($value) {
            return !is_null($value);
        });

        // Replace placeholders with actual values
        $formattedReference = str_replace(array_keys($placeholders), array_values($placeholders), $formatString);

        // Clean up result from residual placeholders and trailing/leading delimiters
        $formattedReference = preg_replace('/\{[^\}]*\}/', '', $formattedReference);  // Remove any unreplaced placeholders
        $formattedReference = preg_replace('/[\/]+/', '/', $formattedReference);  // Replace multiple slashes with a single slash
        $formattedReference = trim($formattedReference, '/');  // Trim slashes from the start and end

        return $formattedReference;
    }

    if (!function_exists('formatText')) {
        function formatText($inputText)
        {
            // Replace " - " with "<br>- " only if " - " is surrounded by spaces
            // $formattedText = preg_replace('/(?<=\s)-\s/', '<br>- ', $inputText);
            // // Remove a "<br>" at the beginning of the string, if present
            // $formattedText = preg_replace('/^<br>/', '', $formattedText);
            // $formattedText = str_replace('&lt;br&gt;', '<br>', $formattedText);
            // return $formattedText;
            return $inputText;
        }
    }

}

if (!function_exists('addCommaToThousand')) {
    function addCommaToThousand($amount)
    {
        // return $amount ? preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $amount) : 0;
        return $amount ? number_format($amount, 2, '.', ',') : '0.00';
    }
}


if (!function_exists('getCurrentProjectStatusMap')) {
    function getCurrentProjectStatusMap()
    {
        $isEnableProjectStatus = GeneralSettingEloquentModel::where('setting', "enable_change_project_status")
        ->where('value', "true")
        ->first();
        $newStatusMapping = [
            'New' => 'New',
            'On-going' => 'InProgress',
            'Deposit Only' => 'InProgress',
            'InProgress' => 'InProgress',
            'Claimed comm' => 'InProgress',
            'Completed' => 'Completed',
            'Cancelled' => 'Cancelled',
            'Pending' => 'Pending'
        ];

        $originalStatusMapping = [
            'New' => 'New',
            'InProgress' => 'InProgress',
            'Claimed comm' => 'InProgress',
            'Completed' => 'Completed',
            'Cancelled' => 'Cancelled',
            'Pending'   => 'Pending'
        ];
        $statusMapping = $isEnableProjectStatus ? $newStatusMapping : $originalStatusMapping;
        return $statusMapping;
    }
}

// if (!function_exists('parseData')) {    
//     function parseData($data, $format, $placeholders)
//     {
//         preg_match_all('/\{([a-zA-Z_]+)(?::([^}]+))?\}/', $format, $matches);
    
//         $parsedData = [];
//         $dataIndex = 0;
//         $formatIndex = 0;
    
//         $usedPlaceholders = [];
//         while ($formatIndex < strlen($format)) {
//             // Match the next placeholder in the format string
//             if (preg_match('/\{([a-zA-Z_]+)(?::([^}]+))?\}/', substr($format, $formatIndex), $match)) {
//                 $placeholder = $match[1];
//                 $formatIndex += strlen($match[0]); // Move index past the placeholder in the format string
    
//                 // Check if the placeholder has a predefined value in $placeholders
//                 if (isset($placeholders[$placeholder])) {
//                     // Get the value for this placeholder from $placeholders
//                     $parsedData[$placeholder] = $placeholders[$placeholder];
//                     $dataIndex += strlen($placeholders[$placeholder]);
//                     $usedPlaceholders[] = $placeholder;
//                 } else {
//                     // Handle dynamic data when no predefined placeholder value is given
//                     // Now, we extract the part of the data that corresponds to the placeholder
//                     if ($placeholder === 'quotation_num' || $placeholder === 'common_quotation_running_num') {
//                         // Manually handle the rest of the data after `quotation_initial`
//                         $remainingData = substr($data, $dataIndex);
//                         $parsedData[$placeholder] = preg_replace('/^\D+/', '', $remainingData); // Remove non-numeric chars
//                         $dataIndex += strlen($parsedData[$placeholder]); // Move the index forward
//                     } else {
//                         // Handle other placeholders dynamically if needed
//                         $remainingData = substr($data, $dataIndex);
//                         $parsedData[$placeholder] = $remainingData;
//                         $dataIndex += strlen($parsedData[$placeholder]);
//                     }
//                 }
//             } else {
//                 // If not a placeholder, move past the static text
//                 $formatIndex++;
//             }
//         }

//         // Handle missing placeholders when format changes
//         foreach ($placeholders as $key => $value) {
//             if (!in_array($key, $usedPlaceholders)) {
//                 // If the placeholder wasn't used, set it to an empty string or skip it
//                 $parsedData[$key] = ''; // Set missing placeholders to empty
//             }
//         }
//         return $parsedData;
//     }    
    
// }

if (!function_exists('parseData')) {
    function parseData($data, $format, $placeholders)
    {
        preg_match_all('/\{([a-zA-Z_]+)(?::([^}]+))?\}/', $format, $matches);

        $parsedData = [];
        $dataIndex = 0;
        $formatIndex = 0;

        $usedPlaceholders = [];

        while ($formatIndex < strlen($format)) {
            // Match the next placeholder in the format string
            if (preg_match('/\{([a-zA-Z_]+)(?::([^}]+))?\}/', substr($format, $formatIndex), $match)) {
                $placeholder = $match[1];
                $lengthSpec = $match[2] ?? null;
                $formatIndex += strlen($match[0]);

                if (isset($placeholders[$placeholder])) {
                    $parsedData[$placeholder] = $placeholders[$placeholder];
                    $dataIndex += strlen($placeholders[$placeholder]);
                    $usedPlaceholders[] = $placeholder;
                } else {
                    $remainingData = substr($data, $dataIndex);

                    if (in_array($placeholder, ['common_quotation_running_num', 'quotation_num'])) {
                        if ($lengthSpec) {
                            $requiredLength = (int) $lengthSpec;
                            if (preg_match('/-?(\d{' . $requiredLength . '})/', $remainingData, $numberMatch)) {
                                $parsedData[$placeholder] = $numberMatch[1];
                                $dataIndex += strlen($numberMatch[0]);
                            }
                        } else {
                            if (preg_match('/(\d+)$/', $remainingData, $numberMatch)) {
                                $parsedData[$placeholder] = $numberMatch[1];
                                $dataIndex += strlen($numberMatch[0]);
                            }
                        }
                    } else {
                        $parsedData[$placeholder] = $remainingData;
                        $dataIndex += strlen($remainingData);
                    }
                }
            } else {
                $formatIndex++;
            }
        }

        foreach ($placeholders as $key => $value) {
            if (!in_array($key, $usedPlaceholders)) {
                $parsedData[$key] = '';
            }
        }

        return $parsedData;
    }
}



if (!function_exists('generateContractItems')) {
    function generateContractItems($projectId)
    {
        $sign_quotation_id = RenovationDocumentsEloquentModel::where('project_id', $projectId)
            ->where('type', 'QUOTATION')
            ->whereNotNull('signed_date')
            ->pluck('id')
            ->first();
        if ($sign_quotation_id) {
            $quotationLists = (new FindAllRenovationDocumentsQuery($sign_quotation_id, 'QUOTATION'))->handle(); // Assuming $quotationLists is a collection and has an 'items' collection.
            $renovation_items = $quotationLists->renovation_items;
            // $renovation_items = $quotationLists->renovation_items; //old one
            $data = $renovation_items->map(function ($item) {
                $areaOfWorkName = isset($item->renovation_area_of_work->name)
                    ? $item->renovation_area_of_work->name
                    : ($item->renovation_item_area_of_work_id
                        ? $item->renovation_area_of_work->areaOfWork->name
                        : '');

                return [
                    'id' => $item->id,
                    'quotation_template_item_id' => $item->quotation_template_item_id,
                    'parent_id' => $item->parent_id,
                    'name' => $item->name,
                    'calculation_type' => $item->renovation_sections->sections->calculation_type,
                    'quantity' => $item->quantity,
                    'current_quantity' => isset($item->current_quantity) ? $item->current_quantity : null,
                    'length' => $item->length,
                    'breadth' => $item->breadth,
                    'height' => $item->height,
                    'measurement' => $item->unit_of_measurement,
                    'is_fixed_measurement' => $item->is_fixed_measurement,
                    'price' => $item->price,
                    'cost_price' => $item->cost_price,
                    'profit_margin' => $item->profit_margin,
                    'is_FOC' => $item->is_FOC,
                    'is_CN' => $item->is_CN,
                    'section_id' => $item->renovation_sections->section_id,
                    'section_name' => $item->renovation_sections->name,
                    'area_of_work_id' => $item->renovation_area_of_work->section_area_of_work_id,
                    'area_of_work_name' => $areaOfWorkName,
                    'is_excluded' => $item->is_excluded
                ];
            });

            $sortQuotation = [];
            // Getting unique section IDs. pluck() gets the values of a given key (column):
            $sectionIds = [];

            foreach ($data as $item) {
                $sectionIds[] = $item['section_id'];
            }

            $originalSections = collect($sectionIds)->unique()->toArray();
            $sectionIndexArray = SectionsIndexEloquentModel::where('document_id', $sign_quotation_id)
                ->pluck('section_sequence')
                ->first();


            $sectionIndexArray = json_decode($sectionIndexArray);


            $uniqueSections = sortArrayBySequenceHelper($originalSections, $sectionIndexArray);

            foreach ($uniqueSections as $sectionId) {
                $filteredItemsBySectionId = $data->filter(function ($item) use ($sectionId) {
                    return $item['section_id'] == $sectionId;
                });


                $emptyAOWItems = $filteredItemsBySectionId->whereNull('area_of_work_id');
                $filterAOWItems = $filteredItemsBySectionId->whereNotNull('area_of_work_id');


                $originalAOWItems = $filterAOWItems->pluck('area_of_work_id')->unique()->toArray();


                $aowIndexData = AOWIndexEloquentModel::where('document_id', $sign_quotation_id)
                    ->where('section_id', $sectionId)
                    ->pluck('aow_sequence')
                    ->first();

                $aowIndexArray = json_decode($aowIndexData);
                $uniqueAOWIds = sortArrayBySequenceHelper($originalAOWItems, $aowIndexArray);

                $hasAOWItems = [];


                $itemsIndexData = ItemsIndexEloquentModel::where('document_id', $sign_quotation_id)
                    ->whereIn('aow_id', $uniqueAOWIds)
                    ->get()
                    ->groupBy('aow_id');

                foreach ($uniqueAOWIds as $aowId) {

                    $itemIndexArray = json_decode(optional($itemsIndexData->get($aowId)->first())->items_sequence ?? '[]');


                    $aowItems = $filterAOWItems->filter(function ($item) use ($aowId) {
                        return $item['area_of_work_id'] == $aowId;
                    })->sortBy(function ($item) use ($itemIndexArray) {
                        return array_search($item['quotation_template_item_id'], $itemIndexArray);
                    });

                    if ($aowItems->isNotEmpty()) {
                        $firstAowItem = $aowItems->first(); // Getting the first item to extract AOW details
                        $aowItems = organizeItemsByParentHelper($aowItems);
                        $objForAOW = [
                            'area_of_work_id' => $aowId,
                            'area_of_work_name' => $firstAowItem['area_of_work_name'],
                            // 'area_of_work_items' => $aowItems->values()->all(), // this is david's code
                            'area_of_work_items' => $aowItems,
                        ];
                        $hasAOWItems[] = $objForAOW;
                    }
                }

                $sectionObj = [
                    'section_id' => $sectionId,
                    'section_name' => $filteredItemsBySectionId->first()['section_name'],
                    'emptyAOWData' => $emptyAOWItems->values()->all(),
                    'hasAOWData' => $hasAOWItems,
                ];

                $sortQuotation[] = $sectionObj;
            }

            return collect($sortQuotation);
        } else {
            return null;
        }
    }
}

if (!function_exists('generateVOItems')) {
    function generateVOItems($projectId)
    {
        $sign_quotation_id = RenovationDocumentsEloquentModel::where('project_id', $projectId)
            ->where('type', 'QUOTATION')
            ->whereNotNull('signed_date')
            ->pluck('id')
            ->first();
        if ($sign_quotation_id) {
            $signedVos = RenovationDocumentsEloquentModel::where('project_id', $projectId)
            ->where('type', 'VARIATIONORDER')
            ->whereNotNull('signed_date')
            ->pluck('id');
            
            $renovation_items = collect();
            if($signedVos->isNotEmpty()){
                foreach ($signedVos as $id) {
                    $voItems = (new FindAllRenovationDocumentsQuery($id, 'VARIATIONORDER'))->handle(); // Assuming $quotationLists is a collection and has an 'items' collection.
                    $renovation_items = $renovation_items->merge($voItems->renovation_items);
                }
            } else {
                return [];
            }
            // $renovation_items = $quotationLists->renovation_items; //old one
            $data = $renovation_items->map(function ($item) {
                $areaOfWorkName = isset($item->renovation_area_of_work->name)
                    ? $item->renovation_area_of_work->name
                    : ($item->renovation_item_area_of_work_id
                        ? $item->renovation_area_of_work->areaOfWork->name
                        : '');

                return [
                    'id' => $item->id,
                    'quotation_template_item_id' => $item->quotation_template_item_id,
                    'parent_id' => $item->parent_id,
                    'name' => $item->name,
                    'calculation_type' => $item->renovation_sections->sections->calculation_type,
                    'quantity' => $item->quantity,
                    'current_quantity' => isset($item->current_quantity) ? $item->current_quantity : null,
                    'length' => $item->length,
                    'breadth' => $item->breadth,
                    'height' => $item->height,
                    'measurement' => $item->unit_of_measurement,
                    'is_fixed_measurement' => $item->is_fixed_measurement,
                    'price' => $item->price,
                    'cost_price' => $item->cost_price,
                    'profit_margin' => $item->profit_margin,
                    'is_FOC' => $item->is_FOC,
                    'is_CN' => $item->is_CN,
                    'section_id' => $item->renovation_sections->section_id,
                    'section_name' => $item->renovation_sections->name,
                    'area_of_work_id' => $item->renovation_area_of_work->section_area_of_work_id,
                    'area_of_work_name' => $areaOfWorkName,
                    'is_excluded' => $item->is_excluded
                ];
            });

            $sortQuotation = [];
            // Getting unique section IDs. pluck() gets the values of a given key (column):
            $sectionIds = [];

            foreach ($data as $item) {
                $sectionIds[] = $item['section_id'];
            }

            $originalSections = collect($sectionIds)->unique()->toArray();
            $sectionIndexArray = SectionsIndexEloquentModel::whereIn('document_id', $signedVos)
                ->pluck('section_sequence')
                ->first();


            $sectionIndexArray = json_decode($sectionIndexArray);


            $uniqueSections = sortArrayBySequenceHelper($originalSections, $sectionIndexArray);

            foreach ($uniqueSections as $sectionId) {
                $filteredItemsBySectionId = $data->filter(function ($item) use ($sectionId) {
                    return $item['section_id'] == $sectionId;
                });


                $emptyAOWItems = $filteredItemsBySectionId->whereNull('area_of_work_id');
                $filterAOWItems = $filteredItemsBySectionId->whereNotNull('area_of_work_id');


                $originalAOWItems = $filterAOWItems->pluck('area_of_work_id')->unique()->toArray();


                $aowIndexData = AOWIndexEloquentModel::whereIn('document_id', $signedVos)
                    ->where('section_id', $sectionId)
                    ->pluck('aow_sequence')
                    ->first();

                $aowIndexArray = json_decode($aowIndexData);
                $uniqueAOWIds = sortArrayBySequenceHelper($originalAOWItems, $aowIndexArray);

                $hasAOWItems = [];


                $itemsIndexData = ItemsIndexEloquentModel::whereIn('document_id', $signedVos)
                    ->whereIn('aow_id', $uniqueAOWIds)
                    ->get()
                    ->groupBy('aow_id');

                foreach ($uniqueAOWIds as $aowId) {

                    $itemIndexArray = json_decode(optional($itemsIndexData->get($aowId)->first())->items_sequence ?? '[]');


                    $aowItems = $filterAOWItems->filter(function ($item) use ($aowId) {
                        return $item['area_of_work_id'] == $aowId;
                    })->sortBy(function ($item) use ($itemIndexArray) {
                        return array_search($item['quotation_template_item_id'], $itemIndexArray);
                    });

                    if ($aowItems->isNotEmpty()) {
                        $firstAowItem = $aowItems->first(); // Getting the first item to extract AOW details
                        $aowItems = organizeItemsByParentHelper($aowItems);

                        $objForAOW = [
                            'area_of_work_id' => $aowId,
                            'area_of_work_name' => $firstAowItem['area_of_work_name'],
                            // 'area_of_work_items' => $aowItems->values()->all(), // this is david's code
                            'area_of_work_items' => $aowItems,
                        ];
                        $hasAOWItems[] = $objForAOW;
                    }
                }

                $sectionObj = [
                    'section_id' => $sectionId,
                    'section_name' => $filteredItemsBySectionId->first()['section_name'],
                    'emptyAOWData' => $emptyAOWItems->values()->all(),
                    'hasAOWData' => $hasAOWItems,
                ];

                $sortQuotation[] = $sectionObj;
            }

            return collect($sortQuotation);
        } else {
            return null;
        }
    }
}

function organizeItemsByParentHelper($items)
{
    $itemMap = [];
    $result = [];

    foreach ($items as $index => $item) {
        $item['items'] = [];
        $uniqueKey = $item['quotation_template_item_id'] . '-' . $index;
        $item['unique_key'] = $uniqueKey;
        $itemMap[$uniqueKey] = $item;

        if (!$item['parent_id']) {
            $result[] = &$itemMap[$uniqueKey];
        }
    }

    foreach ($items as $index => $item) {
        if ($item['parent_id']) {
            foreach ($itemMap as &$parentItem) {
                if ($parentItem['quotation_template_item_id'] === $item['parent_id']) {
                    $parentItem['items'][] = &$itemMap[$item['quotation_template_item_id'] . '-' . $index];
                    break;
                }
            }
        }
    }

    return $result;
}



function sortArrayBySequenceHelper($originalArray, $sequenceArray)
{
    $indexedArray = array_combine(range(0, count($originalArray) - 1), $originalArray);

    // Sort the associative array based on the sequence array
    $sortedAssocArray = [];
    foreach ($sequenceArray as $value) {
        $key = array_search($value, $indexedArray);
        if ($key !== false) {
            $sortedAssocArray[$key] = $value;
        }
    }

    // Return the values of the sorted associative array
    return array_values($sortedAssocArray);
}

if (!function_exists('calculateItemTotalPrice')) {
    function calculateItemTotalPrice($items)
    {
        if ($items['calculation_type'] == 'NORMAL') {
            $final_result = '';
            $totalAmount = floatval($items['quantity']) * floatval($items['price']);

            $totalAmountFormatted = number_format($totalAmount, 2);

            $final_result = $totalAmount == 0 ? '' : $totalAmountFormatted;

            return $items['is_FOC'] ? 'FOC' : preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $final_result);
        } else {
            return $items['is_FOC'] ? 'FOC' : '';
        }
    }
}

if (!function_exists('numberToWords')) {
    function numberToWords($data, $type) {
        if($type == 'sample'){
            $number = $data['total_inclusive'];
        }else{
            $number = $data;
        }
        $units = ['', 'Thousand', 'Million', 'Billion'];
        $words = [];
    
        if ($number === 0) {
            return 'Zero Dollars';
        }
    
        $dollars = floor($number);
        $cents = round(($number - $dollars) * 100);
    
        $chunkCount = 0;
        while ($dollars > 0) {
            $chunk = $dollars % 1000;
            if ($chunk !== 0) {
                array_unshift($words, chunkToWords($chunk) . ' ' . $units[$chunkCount]);
            }
            $dollars = floor($dollars / 1000);
            $chunkCount++;
        }
    
        $result = implode(' ', $words);
    
        if ($cents > 0) {
            $result .= ' Dollars and ' . chunkToWords($cents) . ' Cents';
        } else {
            $result .= ' Dollars';
        }
    
        return $result;
    }
    
    function chunkToWords($chunk) {
        $units = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
        $teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', 'Ten', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
    
        $words = [];
    
        if ($chunk >= 100) {
            $words[] = $units[floor($chunk / 100)] . ' Hundred';
            $chunk %= 100;
        }
    
        if ($chunk >= 20) {
            $words[] = $tens[floor($chunk / 10)];
            $chunk %= 10;
        } elseif ($chunk >= 10) {
            $words[] = $teens[$chunk - 10];
            $chunk = 0;
        }
    
        if ($chunk > 0 && $chunk < 10) {
            $words[] = $units[$chunk];
        }
    
        return implode(' ', $words);
    }

}

if (!function_exists('calculateTotalDiscountAmountHelper')){
    function calculateTotalDiscountAmountHelper($projectId, $type)
    {
        $renovationDocumentIds = [];
        if($type == 'QUOTATION'){
            $renovationDocumentIds = RenovationDocumentsEloquentModel::where('project_id', $projectId)
            ->where('type', 'QUOTATION')
            ->whereNotNull('signed_date')
            ->pluck('id');
        } else if ($type == 'VARIATIONORDER'){
            $renovationDocumentIds = RenovationDocumentsEloquentModel::where('project_id', $projectId)
            ->where('type', 'VARIATIONORDER')
            ->whereNotNull('signed_date')
            ->pluck('id');
        }

        return RenovationDocumentsEloquentModel::whereIn('id', $renovationDocumentIds)
            ->get()
            ->sum(function ($document) {
                if ($document->special_discount_percentage == 0) {
                    return 0; // No discount applied, so discount amount is zero.
                }

                $originalAmount = $document->total_amount / (1 - ($document->special_discount_percentage / 100));
                return $originalAmount - $document->total_amount;
            });
    }
}