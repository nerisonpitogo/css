<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        @page {
            size: A4;
            margin: 0mm;
            margin-top: 0mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 20px;
        }

        /* add table class border collapsed */

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
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
                    {{ number_format($data2['final_rating'], 2) }}%
                    {{ $data2['final_rating_word'] }}
                </td>
            </tr>


        </table>

        <!-- Add a page break to start new content on the next page -->
        <div class="page-break"></div>

        <p>This is content on subsequent pages...</p>

        <!-- Repeat the content as needed -->
    </main>

    <!-- Footer for every page -->
    <footer>
        <img src="{{ base64_image(public_path('storage/report_footer_images/' . $images['report_footer_image'])) }}"
            width="100%">
    </footer>
</body>

</html>
