<?php

use App\Models\Feedback;
use App\Models\Office;
use Illuminate\Support\Facades\DB;

if (!function_exists('toCents')) {
    /**
     * Convert an amount in pesos to cents.
     *
     * @param float $amount The amount in pesos.
     * @return int The amount in cents.
     */
    function toCents($amount)
    {
        return (int) round($amount * 100);
    }
}

if (!function_exists('toPesos')) {
    /**
     * Convert an amount in cents to pesos.
     *
     * @param int $amount The amount in cents.
     * @return float The amount in pesos.
     */
    function toPesos($amount)
    {
        return $amount / 100;
    }
}

// // make get_total_responses function available globally
// if (!function_exists('get_total_responses')) {

//     function get_total_responses($dateFrom, $dateTo, $office, $include_sub_offices)
//     {
//         $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
//         $dateTo = date('Y-m-d 23:59:59', strtotime($dateTo));

//         $officeIds = [$office];
//         if ($include_sub_offices) {
//             $officeIds = get_office_and_sub_offices($office);
//         }

//         $totalResponses = Feedback::join('office_services', 'office_services.id', '=', 'feedbacks.office_service_id')
//             ->whereIn('office_services.office_id', $officeIds)
//             ->whereBetween('feedbacks.created_at', [$dateFrom, $dateTo])->count();
//         return $totalResponses;
//     }
// }

// // get cc awareness where cc1 between 1 and 3
// if (!function_exists('get_cc1_awareness_total')) {

//     function get_cc1_awareness_total($dateFrom, $dateTo, $office, $include_sub_offices)
//     {
//         $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
//         $dateTo = date('Y-m-d 23:59:59', strtotime($dateTo));

//         $officeIds = [$office];
//         if ($include_sub_offices) {
//             $officeIds = get_office_and_sub_offices($office);
//         }

//         $count = Feedback::join('office_services', 'office_services.id', '=', 'feedbacks.office_service_id')
//             ->whereIn('office_services.office_id', $officeIds)
//             ->whereBetween('feedbacks.created_at', [$dateFrom, $dateTo])
//             ->where('cc1', '>=', 1)
//             ->where('cc1', '<=', 3)
//             ->count();

//         return $count;
//     }
// }

// // get cc2 visibility where cc2=1
// if (!function_exists('get_cc2_visibility_total')) {

//     function get_cc2_visibility_total($dateFrom, $dateTo, $office, $include_sub_offices)
//     {
//         $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
//         $dateTo = date('Y-m-d 23:59:59', strtotime($dateTo));

//         $officeIds = [$office];
//         if ($include_sub_offices) {
//             $officeIds = get_office_and_sub_offices($office);
//         }

//         $count = Feedback::join('office_services', 'office_services.id', '=', 'feedbacks.office_service_id')
//             ->whereIn('office_services.office_id', $officeIds)
//             ->whereBetween('feedbacks.created_at', [$dateFrom, $dateTo])
//             ->where('cc2', '=', 1)
//             ->where('cc1', '!=', 4)
//             ->count();

//         return $count;
//     }
// }

// // get cc3 helpfulness where cc3=1 and cc1 !=4
// if (!function_exists('get_cc3_helpfulness_total')) {

//     function get_cc3_helpfulness_total($dateFrom, $dateTo, $office, $include_sub_offices)
//     {
//         $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
//         $dateTo = date('Y-m-d 23:59:59', strtotime($dateTo));

//         $officeIds = [$office];
//         if ($include_sub_offices) {
//             $officeIds = get_office_and_sub_offices($office);
//         }

//         $count = Feedback::join('office_services', 'office_services.id', '=', 'feedbacks.office_service_id')
//             ->whereIn('office_services.office_id', $officeIds)
//             ->whereBetween('feedbacks.created_at', [$dateFrom, $dateTo])
//             ->where('cc3', '=', 1)
//             ->where('cc1', '!=', 4)
//             ->count();

//         return $count;
//     }
// }

// // get sqd group by
// if (!function_exists('get_sqd_all_grouped')) {

//     function get_sqd_all_grouped($dateFrom, $dateTo, $office, $include_sub_offices, $sqd)
//     {
//         $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
//         $dateTo = date('Y-m-d 23:59:59', strtotime($dateTo));

//         $officeIds = [$office];
//         if ($include_sub_offices) {
//             $officeIds = get_office_and_sub_offices($office);
//         }

//         $sqd_group_counter = Feedback::join('office_services', 'office_services.id', '=', 'feedbacks.office_service_id')
//             ->whereIn('office_services.office_id', $officeIds)
//             ->whereBetween('feedbacks.created_at', [$dateFrom, $dateTo])
//             ->select($sqd, DB::raw('count(*) as count'))
//             ->groupBy($sqd)
//             ->get();

//         return $sqd_group_counter;
//     }
// }



// get office_and_sub_offices function
//retruns an array of office ids
if (!function_exists('get_office_and_sub_offices')) {

    function get_office_and_sub_offices($office_id)
    {
        $office = Office::find($office_id);
        if (!$office) {
            return [];
        }

        $officeIds = [];
        collectOfficeIds($office, $officeIds);

        return $officeIds;
    }

    function collectOfficeIds($office, &$officeIds)
    {
        $officeIds[] = $office->id;
        foreach ($office->children as $child) {
            collectOfficeIds($child, $officeIds);
        }
    }
}


if (!function_exists('get_percentage_color')) {

    function get_percentage_color($percentage)
    {
        // below 60 Poor
        // 60-79.9 Fair
        // 80-89.9 Satisfactory
        // 90-94.9 Very Satisfactory
        // 95-100 Outstanding

        if ($percentage === "N/A") {
            return 'text-base-content';
        }


        $class = 'text-success';

        if ($percentage < 60) {
            $class = 'text-error';
        } elseif ($percentage < 80) {
            $class = 'text-warning';
        } elseif ($percentage < 90) {
            $class = 'text-secondary';
        } elseif ($percentage < 95) {
            $class = 'text-primary';
        }

        return $class;
    }
}

if (!function_exists('get_percentage_color_css')) {

    function get_percentage_color_css($percentage)
    {
        // below 60 Poor
        // 60-79.9 Fair
        // 80-89.9 Satisfactory
        // 90-94.9 Very Satisfactory
        // 95-100 Outstanding

        if ($percentage === "N/A") {
            return '#000000'; // Default color for "N/A"
        }

        $color = '#28a745'; // Default color for Outstanding (95-100)

        if ($percentage < 60) {
            $color = '#dc3545'; // Red for Poor
        } elseif ($percentage < 80) {
            $color = '#ffc107'; // Yellow for Fair
        } elseif ($percentage < 90) {
            $color = '#6c757d'; // Gray for Satisfactory
        } elseif ($percentage < 95) {
            $color = '#007bff'; // Blue for Very Satisfactory
        }

        return $color;
    }
}

if (!function_exists('generate_placeholder')) {

    function generate_placeholder($columns, $rows, $class, $height_per_row = 32)
    {
        $string = "<div class='gap-2 " . $class . "'>";

        for ($i = 0; $i < $columns; $i++) {
            $string .= "<div class=''>";
            for ($j = 0; $j < $rows; $j++) {
                $string .= "<div class='bg-base-300 h-" . $height_per_row . " mt-2 w-full skeleton'></div>";
            }
            $string .= "</div>";
        }


        return  $string . "</div>";
    }
}

// return the last office_id that is not null
if (!function_exists('end_office_id')) {
    function end_office_id($array)
    {
        $last = end($array);
        while ($last === null || $last === '') {
            $last = prev($array);
        }
        return $last;
    }
}

// return the last office_id that is not null
if (!function_exists('get_images')) {
    function get_images($office_id)
    {
        $images = [
            'form_header_image' => '',
            'report_header_image' => '',
            'report_footer_image' => '',
        ];

        $office = Office::find($office_id);
        //    if there is at least value in the office header_image, report_header_image, report_footer_image
        if ($office->header_image || $office->report_header_image || $office->report_footer_image) {
            $images['form_header_image'] = $office->header_image;
            $images['report_header_image'] = $office->report_header_image;
            $images['report_footer_image'] = $office->report_footer_image;
        } else {
            $parent = $office->parent;
            while ($parent) {
                if ($parent->header_image || $parent->report_header_image || $parent->report_footer_image) {
                    $images['form_header_image'] = $parent->header_image;
                    $images['report_header_image'] = $parent->report_header_image;
                    $images['report_footer_image'] = $parent->report_footer_image;
                    break;
                }
                $parent = $parent->parent;
            }
        }
        return $images;
    }
}

if (!function_exists('base64_image')) {
    function base64_image($path)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }
}
