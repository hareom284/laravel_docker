<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentStandardEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\EvoItemEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\EvoTemplateItemEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\EvoTemplateRoomEloquentModel;
use stdClass;

class EVOEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $sale_file_path = 'EVO/salesperson_signature_file/' . $this->salesperson_signature;

        $documentStandard = DocumentStandardEloquentModel::where('company_id', $this->projects->company_id)->where('name', "electrical variation order")->first(['header_text', 'footer_text', 'disclaimer']);

        // $allSections = EvoTemplateSectionEloquentModel::get()->toArray();

        // $evoSections = EvoSectionEloquentModel::where('evo_id', $this->id)->get()->keyBy('template_section_id')->toArray();

        // $allData = [];

        $allData = new stdClass;

        $allData->total_price = $this->total_amount;

        $evoTemplateItems = EvoTemplateItemEloquentModel::get()->toArray();

        $evoTemplateRooms = EvoTemplateRoomEloquentModel::get()->toArray();

        $evoItems = EvoItemEloquentModel::with('rooms')->where('evo_id', $this->id)->get()
        ->map(function ($mapItem) {
            // Rename some attributes
            $array = $mapItem->toArray();

            $array['rooms'] = $mapItem->rooms->map(function ($room) {
                return [
                    'id' => $room->id,
                    'room_name' => $room->pivot->name,
                    'quantity' => $room->pivot->quantity,
                    'room_id' => $room->pivot->room_id,
                ];
            })->toArray();

            return $array;
        })
        ->toArray();

        foreach ($evoItems as $items) {

            $checkItemExist = array_search($items['template_item_id'], array_column($evoTemplateItems, 'id'));

            if($checkItemExist !== false)
            {

                $evoTemplateItems[$checkItemExist]['unit_rate_without_gst'] = $items['unit_rate'];
                $evoTemplateItems[$checkItemExist]['unit_rate_with_gst'] = $items['unit_rate'];
                $evoTemplateItems[$checkItemExist]['total_qty'] = $items['quantity'];
                $evoTemplateItems[$checkItemExist]['total_amount'] = $items['total'];
                $evoTemplateItems[$checkItemExist]['rooms'] = $items['rooms'];

            }else{

                $newlyAddedEvoItems = EvoItemEloquentModel::where('evo_id', $this->id)
                                    ->whereNull('template_item_id')
                                    ->with('rooms')
                                    ->get()
                                    ->map(function ($mapItem) {
                                        // Rename some attributes
                                        $array = $mapItem->toArray();

                                        $array['total_qty'] = $array['quantity'];
                                        unset($array['quantity']);

                                        $array['total_amount'] = $array['total'];
                                        unset($array['total']);

                                        $array['description'] = $array['item_description'];
                                        unset($array['item_description']);

                                        $array['unit_rate_without_gst'] = $array['unit_rate'];
                                        $array['unit_rate_with_gst'] = $array['unit_rate'];
                                        unset($array['unit_rate']);

                                        $array['is_new_item'] = true;

                                        $array['rooms'] = $mapItem->rooms->map(function ($room) {
                                            return [
                                                'id' => $room->id,
                                                'room_name' => $room->pivot->name,
                                                'quantity' => $room->pivot->quantity,
                                                'room_id' => $room->pivot->room_id,
                                            ];
                                        })->toArray();

                                        return $array;
                                    })
                                    ->toArray();

                if(!empty($newlyAddedEvoItems)) $evoTemplateItems = array_merge($evoTemplateItems, $newlyAddedEvoItems);

            }

        }

        $allData->items = $evoTemplateItems;

        $allData->rooms = $evoTemplateRooms;

        // array_push($allData, $obj);

        // for ($i = 0; $i < count($allSections); $i++) {
        //     $allSections[$i]['total_price'] = 0;
        //     $allSections[$i]['items'] = EvoTemplateItemEloquentModel::where('evo_section_id', $allSections[$i]['id'])->get()->toArray();
            
        //     if(isset($evoSections[$allSections[$i]['id']])) {
        //         $evoSection = $evoSections[$allSections[$i]['id']];
        //         $allSections[$i]['name'] = $evoSection['name'];
        //         $allSections[$i]['total_price'] = $evoSection['total_price'];

        //         // TODO: Populate selected items
        //         $evoItems = EvoItemEloquentModel::where('evo_section_id', $evoSection['id'])
        //                                         ->whereNotNull('template_item_id')
        //                                         ->with('rooms')
        //                                         ->get()
        //                                         ->map(function ($mapItem) {
        //                                             // Rename some attributes
        //                                             $array = $mapItem->toArray();
        
        //                                             $array['rooms'] = $mapItem->rooms->map(function ($room) {
        //                                                 return [
        //                                                     'id' => $room->id,
        //                                                     'room_name' => $room->pivot->name,
        //                                                     'quantity' => $room->pivot->quantity,
        //                                                     'room_id' => $room->pivot->room_id,
        //                                                 ];
        //                                             })->toArray();
        
        //                                             return $array;
        //                                         })
        //                                         ->keyBy('template_item_id')
        //                                         ->toArray();
        //         for ($y = 0; $y < count($allSections[$i]['items']); $y++) {
        //             if(isset($evoItems[$allSections[$i]['items'][$y]['id']])) {
        //                 $evoItem = $evoItems[$allSections[$i]['items'][$y]['id']];
        //                 $allSections[$i]['items'][$y]['unit_rate_without_gst'] = $evoItem['unit_rate'];
        //                 $allSections[$i]['items'][$y]['unit_rate_with_gst'] = $evoItem['unit_rate'];
        //                 $allSections[$i]['items'][$y]['total_qty'] = $evoItem['quantity'];
        //                 $allSections[$i]['items'][$y]['total_amount'] = $evoItem['total'];
        //                 $allSections[$i]['items'][$y]['rooms'] = $evoItem['rooms'];
        //             }
        //         }

        //         $newlyAddedEvoItems = EvoItemEloquentModel::where('evo_section_id', $evoSection['id'])
        //                                 ->whereNull('template_item_id')
        //                                 ->with('rooms')
        //                                 ->get()
        //                                 ->map(function ($mapItem) {
        //                                     // Rename some attributes
        //                                     $array = $mapItem->toArray();

        //                                     $array['total_qty'] = $array['quantity'];
        //                                     unset($array['quantity']);

        //                                     $array['total_amount'] = $array['total'];
        //                                     unset($array['total']);

        //                                     $array['description'] = $array['item_description'];
        //                                     unset($array['item_description']);

        //                                     $array['unit_rate_without_gst'] = $array['unit_rate'];
        //                                     $array['unit_rate_with_gst'] = $array['unit_rate'];
        //                                     unset($array['unit_rate']);

        //                                     $array['is_new_item'] = true;

        //                                     $array['rooms'] = $mapItem->rooms->map(function ($room) {
        //                                         return [
        //                                             'id' => $room->id,
        //                                             'room_name' => $room->pivot->name,
        //                                             'quantity' => $room->pivot->quantity,
        //                                             'room_id' => $room->pivot->room_id,
        //                                         ];
        //                                     })->toArray();

        //                                     return $array;
        //                                 })
        //                                 ->toArray();

        //         if(!empty($newlyAddedEvoItems))
        //             $allSections[$i]['items'] = array_merge($allSections[$i]['items'], $newlyAddedEvoItems);
        //     }
        //     // else {
        //     //     $allSections[$i]['total_price'] = $evoSections[$allSections[$i]['id']]['total_price'];
                
        //     // }
        //     $allSections[$i]['rooms'] = EvoTemplateRoomEloquentModel::where('evo_section_id', $allSections[$i]['id'])->get()->toArray();
        // }

        return [

            'id' => $this->id,
            'version_num' => $this->version_number,
            'total_amount' => $this->total_amount,
            'grand_total' => $this->grand_total,
            'saleperson_signature' => asset($sale_file_path),
            'signed_date' => $this->signed_date ? $this->signed_date : $this->created_at,
            'already_signed' => $this->signed_date ? true : false,
            'created_date' => $this->created_at,
            'data' => $allData,
            'signed_saleperson' => $this->salesperson->user->first_name . ' ' . $this->salesperson->user->last_name,
            'signed_sale_email' => $this->salesperson->user->email,
            'signed_sale_ph' => $this->salesperson->user->contact_no,
            'rank' => $this->salesperson->rank->rank_name,
            'header_text' => $documentStandard ? $documentStandard->header_text : '',
            'footer_text' => $documentStandard ? $documentStandard->footer_text : '',
            'disclaimer' => $documentStandard ? $documentStandard->disclaimer : '',
        ];

    }
}
