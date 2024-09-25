<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        @page {
            margin: 1cm;
            size: 21cm 29.7cm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 5px;
            padding: 0;
            font-size: 20px;

        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        tr,
        td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
        }

        .outer-container {
            border: 1px solid black;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
            padding: 10px;
            /* Full viewport height */
        }

        .comments-container {
            padding: 10px;
            width: 100%;
            height: 260px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }

        .comment {
            display: inline;
        }

        .separator {
            margin: 0 5px;
        }
    </style>
</head>

<body>
    <!-- First Page Header -->
    <header class="first-page">
        <img src="{{ base64_image(public_path('storage/report_header_images/' . $images['report_header_image'])) }}"
            width="100%">
    </header>

    <!-- Main content of the report -->
    <main>
        <center>
            <p style="font-weight: bold">CLIENT SATISFACTION MEASUREMENT REPORT</p>
            Date Covered: {{ $data['date_covered'] }}

        </center>

        <table style="width:100%">
            <tr>
                <td style="width:50%">OFFICE</td>
                <td style="width:50%">{{ $office->name }}
                    {{ $include_sub_office == 1 ? ' and Sub Offices / Units' : '' }}</td>
            </tr>
            <tr>
                <td style="width:50%">TOTAL NUMBER OF RESPONDENTS</td>
                <td style="width:50%">{{ $data2['total_responses'] }}</td>
            </tr>
        </table>

        <table style="width:100%;margin-top:10px">
            <tr>
                @php
                    $percentage =
                        $data2['total_responses'] > 0
                            ? ($data2['cc1_awareness_total'] / $data2['total_responses']) * 100
                            : 0;
                @endphp

                <td style="width:50%">CC AWARENESS</td>
                <td style="width:50%;color:{{ get_percentage_color_css($percentage) }}">
                    {{ number_format($percentage, 2) }}%
                    or {{ $data2['cc1_awareness_total'] }} out of {{ $data2['total_responses'] }} responses
                </td>
            </tr>

            <tr>
                @php
                    $percentage =
                        $data2['total_responses'] > 0
                            ? ($data2['cc2_visibility_total'] / $data2['total_responses']) * 100
                            : 0;

                @endphp
                <td style="width:50%">CC VISIBILITY</td>
                <td style="width:50%;color:{{ get_percentage_color_css($percentage) }}">
                    {{ number_format($percentage, 2) }}%
                    or {{ $data2['cc2_visibility_total'] }} out of {{ $data2['total_responses'] }} responses
                </td>
            </tr>

            <tr>
                @php
                    $percentage =
                        $data2['total_responses'] > 0
                            ? ($data2['cc3_helpfulness_total'] / $data2['total_responses']) * 100
                            : 0;

                @endphp
                <td style="width:50%">CC VISIBILITY</td>
                <td style="width:50%;color:{{ get_percentage_color_css($percentage) }}">
                    {{ number_format($percentage, 2) }}%
                    or {{ $data2['cc3_helpfulness_total'] }} out of {{ $data2['total_responses'] }} responses
                </td>
            </tr>
        </table>


        <table style="width:100%;margin-top:10px">
            <tr>
                @php
                    $percentage =
                        $data2['total_responses'] > 0
                            ? ($data2['cc1_awareness_total'] / $data2['total_responses']) * 100
                            : 0;
                @endphp

                <td style="width:50%">SERVICE SATISFACTION (SQD0)</td>
                <td style="width:50%;color:{{ get_percentage_color_css($data2['final_rating']) }}">
                    @if ($data2['final_rating'] === 'N/A')
                        N/A
                    @else
                        {{ number_format($data2['final_rating'], 2) }}%
                    @endif
                    [{{ $data2['final_rating_word'] }}]
                </td>
            </tr>

            <tr>

                <td style="width:50%">OVERALL RATING (SQD1 - SQD8)</td>
                <td style="width:50%;color:{{ get_percentage_color_css($data2['overall']['overall']) }}">
                    @if ($data2['overall']['overall'] === 'N/A')
                        N/A
                    @else
                        {{ number_format($data2['overall']['overall'], 2) }}%
                    @endif
                    [{{ $data2['overall']['word'] }}]
                </td>
            </tr>


        </table>

        <table style="margin-top: 10px">
            <tr>
                <th rowspan="2">SQD</th>
                <th style="text-align: center" rowspan="2">CRITERIA</th>
                <th style="text-align: center" colspan="6">RESPONSES</th>
                <th style="text-align: center" rowspan="2">RATING</th>
            </tr>
            <tr>
                <th style="text-align: center">SD</th>
                <th style="text-align: center">D</th>
                <th style="text-align: center">N</th>
                <th style="text-align: center">A</th>
                <th style="text-align: center">SA</th>
                <th style="text-align: center">NA</th>
            </tr>

            <tr>
                <td>SQD0</td>
                <td>SERVICE SATISFACTION</td>
                <td>{{ $data2['sqd0_scores'][2][1] }}</td>
                <td>{{ $data2['sqd0_scores'][2][2] }}</td>
                <td>{{ $data2['sqd0_scores'][2][3] }}</td>
                <td>{{ $data2['sqd0_scores'][2][4] }}</td>
                <td>{{ $data2['sqd0_scores'][2][5] }}</td>
                <td>{{ $data2['sqd0_scores'][2][6] }}</td>
                <td style="color:{{ get_percentage_color_css($data2['sqd0_scores'][0]) }}">
                    @if ($data2['sqd0_scores'][0] === 'N/A')
                        N/A
                    @else
                        {{ number_format($data2['sqd0_scores'][0], 2) }}
                    @endif

                    [{{ $data2['sqd0_scores'][1] }}]
                </td>
            </tr>

            <tr>
                <td>SQD1</td>
                <td>RESPONSIVENESS</td>
                <td>{{ $data2['sqd1_scores'][2][1] }}</td>
                <td>{{ $data2['sqd1_scores'][2][2] }}</td>
                <td>{{ $data2['sqd1_scores'][2][3] }}</td>
                <td>{{ $data2['sqd1_scores'][2][4] }}</td>
                <td>{{ $data2['sqd1_scores'][2][5] }}</td>
                <td>{{ $data2['sqd1_scores'][2][6] }}</td>
                <td style="color:{{ get_percentage_color_css($data2['sqd1_scores'][0]) }}">
                    @if ($data2['sqd1_scores'][0] === 'N/A')
                        N/A
                    @else
                        {{ number_format($data2['sqd1_scores'][0], 2) }}
                    @endif
                    [{{ $data2['sqd1_scores'][1] }}]
                </td>
            </tr>

            <tr>
                <td>SQD2</td>
                <td>RELIABILITY</td>
                <td>{{ $data2['sqd2_scores'][2][1] }}</td>
                <td>{{ $data2['sqd2_scores'][2][2] }}</td>
                <td>{{ $data2['sqd2_scores'][2][3] }}</td>
                <td>{{ $data2['sqd2_scores'][2][4] }}</td>
                <td>{{ $data2['sqd2_scores'][2][5] }}</td>
                <td>{{ $data2['sqd2_scores'][2][6] }}</td>
                <td style="color:{{ get_percentage_color_css($data2['sqd2_scores'][0]) }}">
                    @if ($data2['sqd2_scores'][0] === 'N/A')
                        N/A
                    @else
                        {{ number_format($data2['sqd2_scores'][0], 2) }}
                    @endif
                    [{{ $data2['sqd2_scores'][1] }}]
                </td>
            </tr>

            <tr>
                <td>SQD3</td>
                <td>ACCESS AND FACILITIES</td>
                <td>{{ $data2['sqd3_scores'][2][1] }}</td>
                <td>{{ $data2['sqd3_scores'][2][2] }}</td>
                <td>{{ $data2['sqd3_scores'][2][3] }}</td>
                <td>{{ $data2['sqd3_scores'][2][4] }}</td>
                <td>{{ $data2['sqd3_scores'][2][5] }}</td>
                <td>{{ $data2['sqd3_scores'][2][6] }}</td>
                <td style="color:{{ get_percentage_color_css($data2['sqd3_scores'][0]) }}">
                    @if ($data2['sqd3_scores'][0] === 'N/A')
                        N/A
                    @else
                        {{ number_format($data2['sqd3_scores'][0], 2) }}
                    @endif
                    [{{ $data2['sqd3_scores'][1] }}]
                </td>
            </tr>

            <tr>
                <td>SQD4</td>
                <td>COMMUNICATION</td>
                <td>{{ $data2['sqd4_scores'][2][1] }}</td>
                <td>{{ $data2['sqd4_scores'][2][2] }}</td>
                <td>{{ $data2['sqd4_scores'][2][3] }}</td>
                <td>{{ $data2['sqd4_scores'][2][4] }}</td>
                <td>{{ $data2['sqd4_scores'][2][5] }}</td>
                <td>{{ $data2['sqd4_scores'][2][6] }}</td>
                <td style="color:{{ get_percentage_color_css($data2['sqd4_scores'][0]) }}">
                    @if ($data2['sqd4_scores'][0] === 'N/A')
                        N/A
                    @else
                        {{ number_format($data2['sqd4_scores'][0], 2) }}
                    @endif
                    [{{ $data2['sqd4_scores'][1] }}]
                </td>
            </tr>

            <tr>
                <td>SQD5</td>
                <td>COST</td>
                <td>{{ $data2['sqd5_scores'][2][1] }}</td>
                <td>{{ $data2['sqd5_scores'][2][2] }}</td>
                <td>{{ $data2['sqd5_scores'][2][3] }}</td>
                <td>{{ $data2['sqd5_scores'][2][4] }}</td>
                <td>{{ $data2['sqd5_scores'][2][5] }}</td>
                <td>{{ $data2['sqd5_scores'][2][6] }}</td>
                <td style="color:{{ get_percentage_color_css($data2['sqd5_scores'][0]) }}">
                    @if ($data2['sqd5_scores'][0] === 'N/A')
                        N/A
                    @else
                        {{ number_format($data2['sqd5_scores'][0], 2) }}
                    @endif
                    [{{ $data2['sqd5_scores'][1] }}]
                </td>
            </tr>

            <tr>
                <td>SQD6</td>
                <td>INTEGRITY</td>
                <td>{{ $data2['sqd6_scores'][2][1] }}</td>
                <td>{{ $data2['sqd6_scores'][2][2] }}</td>
                <td>{{ $data2['sqd6_scores'][2][3] }}</td>
                <td>{{ $data2['sqd6_scores'][2][4] }}</td>
                <td>{{ $data2['sqd6_scores'][2][5] }}</td>
                <td>{{ $data2['sqd6_scores'][2][6] }}</td>
                <td style="color:{{ get_percentage_color_css($data2['sqd6_scores'][0]) }}">
                    @if ($data2['sqd6_scores'][0] === 'N/A')
                        N/A
                    @else
                        {{ number_format($data2['sqd6_scores'][0], 2) }}
                    @endif
                    [{{ $data2['sqd6_scores'][1] }}]
                </td>
            </tr>

            <tr>
                <td>SQD7</td>
                <td>ASSURANCE</td>
                <td>{{ $data2['sqd7_scores'][2][1] }}</td>
                <td>{{ $data2['sqd7_scores'][2][2] }}</td>
                <td>{{ $data2['sqd7_scores'][2][3] }}</td>
                <td>{{ $data2['sqd7_scores'][2][4] }}</td>
                <td>{{ $data2['sqd7_scores'][2][5] }}</td>
                <td>{{ $data2['sqd7_scores'][2][6] }}</td>
                <td style="color:{{ get_percentage_color_css($data2['sqd7_scores'][0]) }}">
                    @if ($data2['sqd7_scores'][0] === 'N/A')
                        N/A
                    @else
                        {{ number_format($data2['sqd7_scores'][0], 2) }}
                    @endif
                    [{{ $data2['sqd7_scores'][1] }}]
                </td>
            </tr>

            <tr>
                <td>SQD8</td>
                <td>OUTCOME</td>
                <td>{{ $data2['sqd8_scores'][2][1] }}</td>
                <td>{{ $data2['sqd8_scores'][2][2] }}</td>
                <td>{{ $data2['sqd8_scores'][2][3] }}</td>
                <td>{{ $data2['sqd8_scores'][2][4] }}</td>
                <td>{{ $data2['sqd8_scores'][2][5] }}</td>
                <td>{{ $data2['sqd8_scores'][2][6] }}</td>
                <td style="color:{{ get_percentage_color_css($data2['sqd8_scores'][0]) }}">
                    @if ($data2['sqd8_scores'][0] === 'N/A')
                        N/A
                    @else
                        {{ number_format($data2['sqd8_scores'][0], 2) }}
                    @endif
                    [{{ $data2['sqd8_scores'][1] }}]
                </td>
            </tr>

        </table>

        <div class="outer-container">
            <div class="comments-container">
                @php
                    $firstReported = true;
                @endphp

                @foreach ($data2['comments'] as $comment)
                    @if ($comment->is_reported)
                        @if (!$firstReported)
                            <span class="separator">*</span>
                        @endif
                        <span class="comment"
                            style="color: 
                @if ($comment->type == 1) green 
                @elseif ($comment->type == 2) red 
                @else black @endif;">
                            {{ $comment->suggestions }}
                        </span>
                        @php
                            $firstReported = false;
                        @endphp
                    @endif
                @endforeach
            </div>
        </div>

        <table style="border:none;margin-top:10px">
            <tr style="border:none">
                <td style="border:none">Prepared by:</td>
                <td style="border:none"></td>
                <td style="border:none">Attested by:</td>
                <td style="border:none"></td>
            </tr>
            <tr style="border:none;height:50px">
                <td style="border:none"></td>
                <td style="border:none;text-align: center">
                    <span style="text-decoration: underline">{{ $images['prepared_by_name'] }}</span>
                    <br>
                    {!! str_replace('|', '<br>', $images['prepared_by_position']) !!}
                </td>
                <td style="border:none"></td>
                <td style="border:none;text-align: center">
                    <span style="text-decoration: underline">{{ $images['attested_by_name'] }}</span>
                    <br>
                    {!! str_replace('|', '<br>', $images['attested_by_position']) !!}
                </td>
            </tr>
        </table>


    </main>

    <!-- Footer for every page -->
    <footer>
        <img src="{{ base64_image(public_path('storage/report_footer_images/' . $images['report_footer_image'])) }}"
            width="100%">
    </footer>

    <!-- Page break -->
    <div style="page-break-before: always;"></div>
    <br>
    <br>
    <table>
        <tr>
            <th class="">D1. Age</th>
            <th class="">External</th>
            <th class="">Internal</th>
            <th class="">Overall</th>
        </tr>
        @foreach ($data2['age_brackets_with_percentage'] as $age_bracket)
            <tr>

                <td>{{ $age_bracket['label'] }}</td>

                <td>{{ $age_bracket['external'] }}
                    ({{ number_format($age_bracket['percentage_external'], 2) }}%)
                </td>

                <td>{{ $age_bracket['internal'] }}
                    ({{ number_format($age_bracket['percentage_internal'], 2) }}%)</td>

                <td>{{ $age_bracket['overall'] }}
                    ({{ number_format($age_bracket['percentage_overall'], 2) }}%)</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="4"></td>
        </tr>
        <tr>
            <th>D2. Sex</th>
            <th>External</th>
            <th>Internal</th>
            <th>Overall</th>
        </tr>
        @foreach ($data2['sex_bracket_with_percentage'] as $sex => $bracket)
            <tr>
                <td>{{ ucfirst($sex) }}</td>
                <td>
                    {{ $bracket['external'] }}
                    ({{ number_format($bracket['percentage_external'], 2) }}%)
                </td>
                <td>
                    {{ $bracket['internal'] }}
                    ({{ number_format($bracket['percentage_internal'], 2) }}%)
                </td>
                <td>
                    {{ $bracket['overall'] }}
                    ({{ number_format($bracket['percentage_overall'], 2) }}%)
                </td>
            </tr>
        @endforeach


        <tr>
            <td colspan="4"></td>
        </tr>
        <tr>
            <th>D2. Region</th>
            <th>External</th>
            <th>Internal</th>
            <th>Overall</th>
        </tr>
        @foreach ($data2['region_responses_with_percentage'] as $region_id => $data)
            <tr>
                <td>{{ $data['region_name'] }}</td>
                <td>
                    {{ $data['internal'] }} ({{ number_format($data['percentage_internal'], 2) }}%)
                </td>
                <td>
                    {{ $data['external'] }} ({{ number_format($data['percentage_external'], 2) }}%)
                </td>
                <td>
                    {{ $data['overall'] }} ({{ number_format($data['percentage_overall'], 2) }}%)
                </td>
            </tr>
        @endforeach
    </table>


    <table style="margin-top:10px">
        <thead>
            <tr>
                <th>External Services</th>
                <th>Responses</th>
            </tr>
        </thead>
        <tbody>
            @if (count($data2['external_services']) == 0)
                <tr>
                    <td colspan="2" class="">No data available</td>
                </tr>
            @endif
            @foreach ($data2['external_services'] as $service)
                <tr>
                    <td>{{ $service['service_name'] }}</td>
                    <td>{{ $service['total_responses'] }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>

    <table style="margin-top:10px">

        <thead>
            <tr>
                <th>Internal Services</th>
                <th>Responses</th>
            </tr>
        </thead>
        <tbody>
            @if (count($data2['internal_services']) == 0)
                <tr>
                    <td colspan="2" class="">No data available</td>
                </tr>
            @endif
            @foreach ($data2['internal_services'] as $service)
                <tr>
                    <td>{{ $service['service_name'] }}</td>
                    <td>{{ $service['total_responses'] }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>

    <table style="margin-top:10px">

        <thead>
            <tr>
                <th colspan="2">Services with no
                    responses</th>
            </tr>
        </thead>

        <tbody>
            @if (count($data2['services_with_no_responses']) == 0)
                <tr>
                    <td colspan="2" class="">No data available</td>
                </tr>
            @endif
            @foreach ($data2['services_with_no_responses'] as $service)
                <tr>
                    <td colspan='2'>{{ $service['service_name'] }}</td>
                </tr>
            @endforeach

        </tbody>

    </table>

</body>

</html>
