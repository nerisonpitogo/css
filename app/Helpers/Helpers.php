<?php

use App\Models\Feedback;
use App\Models\Office;

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

// make get_total_responses function available globally
if (!function_exists('get_total_responses')) {

    function get_total_responses($dateFrom, $dateTo, $office, $include_sub_offices)
    {
        $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
        $dateTo = date('Y-m-d 23:59:59', strtotime($dateTo));

        $officeIds = [$office];
        if ($include_sub_offices) {
            $officeIds = get_office_and_sub_offices($office);
        }

        $totalResponses = Feedback::join('office_services', 'office_services.id', '=', 'feedbacks.office_service_id')
            ->whereIn('office_services.office_id', $officeIds)
            ->whereBetween('feedbacks.created_at', [$dateFrom, $dateTo])->count();
        return $totalResponses;
    }
}

// get cc awareness where cc1 between 1 and 3
if (!function_exists('get_cc1_awareness_total')) {

    function get_cc1_awareness_total($dateFrom, $dateTo, $office, $include_sub_offices)
    {
        $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
        $dateTo = date('Y-m-d 23:59:59', strtotime($dateTo));

        $officeIds = [$office];
        if ($include_sub_offices) {
            $officeIds = get_office_and_sub_offices($office);
        }

        $count = Feedback::join('office_services', 'office_services.id', '=', 'feedbacks.office_service_id')
            ->whereIn('office_services.office_id', $officeIds)
            ->whereBetween('feedbacks.created_at', [$dateFrom, $dateTo])
            ->where('cc1', '>=', 1)
            ->where('cc1', '<=', 3)
            ->count();

        return $count;
    }
}

// get cc2 visibility where cc2=1
if (!function_exists('get_cc2_visibility_total')) {

    function get_cc2_visibility_total($dateFrom, $dateTo, $office, $include_sub_offices)
    {
        $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
        $dateTo = date('Y-m-d 23:59:59', strtotime($dateTo));

        $officeIds = [$office];
        if ($include_sub_offices) {
            $officeIds = get_office_and_sub_offices($office);
        }

        $count = Feedback::join('office_services', 'office_services.id', '=', 'feedbacks.office_service_id')
            ->whereIn('office_services.office_id', $officeIds)
            ->whereBetween('feedbacks.created_at', [$dateFrom, $dateTo])
            ->where('cc2', '=', 1)
            ->where('cc1', '!=', 4)
            ->count();

        return $count;
    }
}

// get cc3 helpfulness where cc3=1 and cc1 !=4
if (!function_exists('get_cc3_helpfulness_total')) {

    function get_cc3_helpfulness_total($dateFrom, $dateTo, $office, $include_sub_offices)
    {
        $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
        $dateTo = date('Y-m-d 23:59:59', strtotime($dateTo));

        $officeIds = [$office];
        if ($include_sub_offices) {
            $officeIds = get_office_and_sub_offices($office);
        }

        $count = Feedback::join('office_services', 'office_services.id', '=', 'feedbacks.office_service_id')
            ->whereIn('office_services.office_id', $officeIds)
            ->whereBetween('feedbacks.created_at', [$dateFrom, $dateTo])
            ->where('cc3', '=', 1)
            ->where('cc1', '!=', 4)
            ->count();

        return $count;
    }
}



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
        if ($percentage < 30) {
            $class = 'text-error';
        } elseif ($percentage < 50) {
            $class = 'text-warning';
        } elseif ($percentage < 80) {
            $class = 'text-primary';
        } else {
            $class = 'text-success';
        }
        return $class;
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
