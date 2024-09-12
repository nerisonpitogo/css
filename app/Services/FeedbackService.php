<?php

namespace App\Services;

use App\Models\Feedback;
use App\Models\Office;
use Illuminate\Support\Facades\DB;

class FeedbackService
{

    // make get_total_responses function available globally

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


    // get cc awareness where cc1 between 1 and 3
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


    // get cc2 visibility where cc2=1


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


    // get cc3 helpfulness where cc3=1 and cc1 !=4
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


    // get sqd group by answer
    function get_sqd_all_grouped_by_answer($dateFrom, $dateTo, $office, $include_sub_offices, $sqd)
    {
        $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
        $dateTo = date('Y-m-d 23:59:59', strtotime($dateTo));

        $officeIds = [$office];
        if ($include_sub_offices) {
            $officeIds = get_office_and_sub_offices($office);
        }

        $sqd_group_by_answer = Feedback::join('office_services', 'office_services.id', '=', 'feedbacks.office_service_id')
            ->whereIn('office_services.office_id', $officeIds)
            ->whereBetween('feedbacks.created_at', [$dateFrom, $dateTo])
            ->select($sqd, DB::raw('count(*) as count'))
            ->groupBy($sqd)
            ->get();

        return $sqd_group_by_answer;
    }

    function get_sqd_score_grouped_array($dateFrom, $dateTo, $office, $include_sub_offices, $sqd): array
    {
        // 1 to 6
        $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
        $dateTo = date('Y-m-d 23:59:59', strtotime($dateTo));

        $officeIds = [$office];
        if ($include_sub_offices) {
            $officeIds = get_office_and_sub_offices($office);
        }

        $sqd_group_counter = Feedback::join('office_services', 'office_services.id', '=', 'feedbacks.office_service_id')
            ->whereIn('office_services.office_id', $officeIds)
            ->whereBetween('feedbacks.created_at', [$dateFrom, $dateTo])
            ->select($sqd, DB::raw('count(*) as count'))
            ->groupBy($sqd)
            ->get();

        $array_results = [];
        $array_results[0] = 0; //empty
        $array_results[1] = 0; //Strongly Disagree
        $array_results[2] = 0; //Disagree
        $array_results[3] = 0; //Neutral
        $array_results[4] = 0; //Agree
        $array_results[5] = 0; //Strongly Agree
        $array_results[6] = 0; //Not Applicable

        foreach ($sqd_group_counter as $sqd_group) {
            // add only if $sqd_group->$sqd is between 1 and 6
            if ($sqd_group->$sqd >= 1 && $sqd_group->$sqd <= 6) {
                $array_results[$sqd_group->$sqd] = $sqd_group->count;
            }
            // $array_results[$sqd_group->$sqd] = $sqd_group->count;
        }

        return $array_results;
    }

    function get_sqd_score_overall($dateFrom, $dateTo, $office, $include_sub_offices, $sqd)
    {
        // 1 to 6
        $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
        $dateTo = date('Y-m-d 23:59:59', strtotime($dateTo));

        $officeIds = [$office];
        if ($include_sub_offices) {
            $officeIds = get_office_and_sub_offices($office);
        }

        $sqd_group_counter = Feedback::join('office_services', 'office_services.id', '=', 'feedbacks.office_service_id')
            ->whereIn('office_services.office_id', $officeIds)
            ->whereBetween('feedbacks.created_at', [$dateFrom, $dateTo])
            ->select($sqd, DB::raw('count(*) as count'))
            ->groupBy($sqd)
            ->get();

        $array_results = [];
        $array_results[0] = 0; //empty
        $array_results[1] = 0; //Strongly Disagree
        $array_results[2] = 0; //Disagree
        $array_results[3] = 0; //Neutral
        $array_results[4] = 0; //Agree
        $array_results[5] = 0; //Strongly Agree
        $array_results[6] = 0; //Not Applicable

        foreach ($sqd_group_counter as $sqd_group) {
            // add only if $sqd_group->$sqd is between 1 and 6
            if ($sqd_group->$sqd >= 1 && $sqd_group->$sqd <= 6) {
                $array_results[$sqd_group->$sqd] = $sqd_group->count;
            }
            // $array_results[$sqd_group->$sqd] = $sqd_group->count;
        }

        return $this->get_score_from_array_results($array_results);
    }

    function get_sqd_score_overall_with_word($dateFrom, $dateTo, $office, $include_sub_offices, $sqd)
    {
        // return array[value, word]
        $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
        $dateTo = date('Y-m-d 23:59:59', strtotime($dateTo));

        $officeIds = [$office];
        if ($include_sub_offices) {
            $officeIds = get_office_and_sub_offices($office);
        }

        $sqd_group_counter = Feedback::join('office_services', 'office_services.id', '=', 'feedbacks.office_service_id')
            ->whereIn('office_services.office_id', $officeIds)
            ->whereBetween('feedbacks.created_at', [$dateFrom, $dateTo])
            ->select($sqd, DB::raw('count(*) as count'))
            ->groupBy($sqd)
            ->get();

        $array_results = [];
        $array_results[0] = 0; //empty
        $array_results[1] = 0; //Strongly Disagree
        $array_results[2] = 0; //Disagree
        $array_results[3] = 0; //Neutral
        $array_results[4] = 0; //Agree
        $array_results[5] = 0; //Strongly Agree
        $array_results[6] = 0; //Not Applicable

        foreach ($sqd_group_counter as $sqd_group) {
            // add only if $sqd_group->$sqd is between 1 and 6
            if ($sqd_group->$sqd >= 1 && $sqd_group->$sqd <= 6) {
                $array_results[$sqd_group->$sqd] = $sqd_group->count;
            }
            // $array_results[$sqd_group->$sqd] = $sqd_group->count;
        }

        return [
            $this->get_score_from_array_results($array_results),
            $this->get_rating_in_words($this->get_score_from_array_results($array_results))
        ];
    }





    function get_sqd1_to_sqd8_array($dateFrom, $dateTo, $office, $include_sub_offices)
    {
        $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
        $dateTo = date('Y-m-d 23:59:59', strtotime($dateTo));

        $officeIds = [$office];
        if ($include_sub_offices) {
            $officeIds = get_office_and_sub_offices($office);
        }

        $sqd1 = $this->get_sqd_score_grouped_array($dateFrom, $dateTo, $office, $include_sub_offices, 'sqd1');
        $sqd2 = $this->get_sqd_score_grouped_array($dateFrom, $dateTo, $office, $include_sub_offices, 'sqd2');
        $sqd3 = $this->get_sqd_score_grouped_array($dateFrom, $dateTo, $office, $include_sub_offices, 'sqd3');
        $sqd4 = $this->get_sqd_score_grouped_array($dateFrom, $dateTo, $office, $include_sub_offices, 'sqd4');
        $sqd5 = $this->get_sqd_score_grouped_array($dateFrom, $dateTo, $office, $include_sub_offices, 'sqd5');
        $sqd6 = $this->get_sqd_score_grouped_array($dateFrom, $dateTo, $office, $include_sub_offices, 'sqd6');
        $sqd7 = $this->get_sqd_score_grouped_array($dateFrom, $dateTo, $office, $include_sub_offices, 'sqd7');
        $sqd8 = $this->get_sqd_score_grouped_array($dateFrom, $dateTo, $office, $include_sub_offices, 'sqd8');

        return [
            'sqd1' => $sqd1,
            'sqd2' => $sqd2,
            'sqd3' => $sqd3,
            'sqd4' => $sqd4,
            'sqd5' => $sqd5,
            'sqd6' => $sqd6,
            'sqd7' => $sqd7,
            'sqd8' => $sqd8,
        ];
    }

    function get_sqd1_to_sqd8_overall_and_word($dateFrom, $dateTo, $office, $include_sub_offices)
    {
        $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
        $dateTo = date('Y-m-d 23:59:59', strtotime($dateTo));



        $sqd1 = $this->get_sqd_score_grouped_array($dateFrom, $dateTo, $office, $include_sub_offices, 'sqd1');
        $sqd2 = $this->get_sqd_score_grouped_array($dateFrom, $dateTo, $office, $include_sub_offices, 'sqd2');
        $sqd3 = $this->get_sqd_score_grouped_array($dateFrom, $dateTo, $office, $include_sub_offices, 'sqd3');
        $sqd4 = $this->get_sqd_score_grouped_array($dateFrom, $dateTo, $office, $include_sub_offices, 'sqd4');
        $sqd5 = $this->get_sqd_score_grouped_array($dateFrom, $dateTo, $office, $include_sub_offices, 'sqd5');
        $sqd6 = $this->get_sqd_score_grouped_array($dateFrom, $dateTo, $office, $include_sub_offices, 'sqd6');
        $sqd7 = $this->get_sqd_score_grouped_array($dateFrom, $dateTo, $office, $include_sub_offices, 'sqd7');
        $sqd8 = $this->get_sqd_score_grouped_array($dateFrom, $dateTo, $office, $include_sub_offices, 'sqd8');


        $total_strongly_agree = $sqd1[5] + $sqd2[5] + $sqd3[5] + $sqd4[5] + $sqd5[5] + $sqd6[5] + $sqd7[5] + $sqd8[5];
        $total_agree = $sqd1[4] + $sqd2[4] + $sqd3[4] + $sqd4[4] + $sqd5[4] + $sqd6[4] + $sqd7[4] + $sqd8[4];
        $total_neutral = $sqd1[3] + $sqd2[3] + $sqd3[3] + $sqd4[3] + $sqd5[3] + $sqd6[3] + $sqd7[3] + $sqd8[3];
        $total_disagree = $sqd1[2] + $sqd2[2] + $sqd3[2] + $sqd4[2] + $sqd5[2] + $sqd6[2] + $sqd7[2] + $sqd8[2];
        $total_strongly_disagree = $sqd1[1] + $sqd2[1] + $sqd3[1] + $sqd4[1] + $sqd5[1] + $sqd6[1] + $sqd7[1] + $sqd8[1];
        $total_na_answer = $sqd1[6] + $sqd2[6] + $sqd3[6] + $sqd4[6] + $sqd5[6] + $sqd6[6] + $sqd7[6] + $sqd8[6];

        $total = $total_strongly_agree + $total_agree + $total_neutral + $total_disagree + $total_strongly_disagree + $total_na_answer;

        $overall_score = $this->get_score($total_strongly_agree, $total_agree, $total, $total_na_answer);

        return [
            'overall' => $overall_score,
            'word' => $this->get_rating_in_words($overall_score)
        ];
    }



    function get_score_from_array_results($array_results)
    {
        // $strongly_disagree = $array_results[1];
        // $disagree = $array_results[2];
        // $neutral = $array_results[3];
        $agree = $array_results[4];
        $strongly_agree = $array_results[5];
        $na_answer = $array_results[6];
        $total = array_sum($array_results);

        return $this->get_score($strongly_agree, $agree, $total, $na_answer);
    }

    function get_score($strongly_agree, $agree,  $total, $na_answer)
    {
        if ($total - $na_answer == 0) {
            return "N/A";
        }
        return ($strongly_agree + $agree) / ($total - $na_answer) * 100;
    }

    function get_rating_in_words($rating)
    {
        // below 60 Poor
        // 60-79.9 Fair
        // 80-89.9 Satisfactory
        // 90-94.9 Very Satisfactory
        // 95-100 Outstanding


        if ($rating == "N/A") {
            return "";
        }

        if ($rating < 60) {
            return 'Poor';
        } elseif ($rating >= 60 && $rating < 80) {
            return 'Fair';
        } elseif ($rating >= 80 && $rating < 90) {
            return 'Satisfactory';
        } elseif ($rating >= 90 && $rating < 95) {
            return 'Very Satisfactory';
        } elseif ($rating >= 95) {
            return 'Outstanding';
        }
    }
}
