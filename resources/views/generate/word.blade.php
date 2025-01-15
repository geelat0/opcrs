<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OPCR</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
        }

        .header_page {
            font-size: 10px;
        }

        .subheader {
            font-weight: bold;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }

        .table th, .table td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #8ccdf0;
            font-size: 12px;
            text-align: center;
        }

        .table td {
            vertical-align: top;
            font-size: 11px;
            /* page-break-inside: avoid; */
        }

        .no-indicators td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        .footer {
            text-align: right;
            font-size: 12px;
            position: fixed;
            bottom: 20px;
            width: 100%;
        }

        .page-number:before {
            content: counter(page);
        }

        .avoid-page-break {
            page-break-inside: avoid;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    <div class="container">
        <table class="table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 14%;">Organizational Outcome/PAP</th>
                    <th rowspan="2" style="width: 18.5%;">Success Indicator</th>
                    <th rowspan="2">Allotted Budget</th>
                    <th rowspan="2" style="width: 15%;">Division/Individuals Accountable</th>
                    <th rowspan="2" style="width: 18%;">Actual Accomplishment</th>
                    <th colspan="4">Rating</th>
                    <th rowspan="2" style="width: 13%;">Remarks</th>
                </tr>
                <tr class="rating-header">
                    <th>Q1</th>
                    <th>Q2</th>
                    <th>T3</th>
                    <th>A4</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="10" style="background-color: #8ccdf0; font-weight: bold; text-align: left;">
                        Core
                    </td>
                </tr>
                @if(!empty($groupedOutcomes['Core']))
                    @foreach ($groupedOutcomes['Core'] as $outcome)
                        @php
                            $hasIndicators = count($outcome->successIndicators) > 0;
                        @endphp
                        @if(!$hasIndicators)
                            <tr class="no-indicators">
                                <td colspan="1">
                                    {{ $outcome->organizational_outcome }}
                                </td>
                                <td colspan="9"></td>
                            </tr>

                        @else
                            @foreach($outcome->successIndicators as $index => $indicator)
                                    @php
                                        $division_ids = json_decode($indicator->division_id);
                                        $filteredDivisionIds = $divisionIds ?? []; // The filtered division IDs from the request
                                        $entriesForIndicator = $entries[$indicator->id] ?? collect();
                                    @endphp

                                    @if($index > 0 && $index % 10 === 0)
                                    <!-- Add a page break every 10 rows for large tables -->
                                    <tr class="page-break"><td colspan="10"></td></tr>
                                    @endif
                                <tr>
                                    
                                    @if($index === 0)
                                    {{-- <td></td> --}}
                                    <td rowspan="{{ count($outcome->successIndicators) }}">{{ $outcome->organizational_outcome }}</td>

                                    @endif
                                    <td>
                                        @if(empty($filteredDivisionIds))
                                            <!-- If no division is filtered, show the target and measures -->
                                            {{ '(' . ($indicator->target == 0 ? 'Actual' : $indicator->target) . ') ' . $indicator->measures }}
                                        @else
                                            <!-- If division is filtered, show the specific budget based on division -->
                                            @foreach($division_ids as $divisionId)
                                                @php
                                                    $division = \App\Models\Division::find($divisionId);
                                                    $showDivision = in_array($divisionId, $filteredDivisionIds);
                                                @endphp

                                                @if($showDivision && $division)
                                                    @switch($division->division_name)
                                                        @case('Albay PO')
                                                            {{ '(' . ($indicator->Albay_target  == 0 ? 'Actual' : $indicator->Albay_target ) . ') ' . $indicator->measures }}
                                                            @break
                                                        @case('Camarines Norte PO')
                                                            {{ '(' . ($indicator->Camarines_Norte_target == 0 ? 'Actual' : $indicator->Camarines_Norte_target) . ') ' . $indicator->measures }}
                                                            @break
                                                        @case('Camarines Sur PO')
                                                            {{ '(' . ($indicator->Camarines_Sur_target == 0 ? 'Actual' :  $indicator->Camarines_Sur_target) . ') ' . $indicator->measures }}
                                                            @break
                                                        @case('Catanduanes PO')
                                                            {{ '(' . ($indicator->Catanduanes_target == 0 ? 'Actual' : $indicator->Catanduanes_target) . ') ' . $indicator->measures }}
                                                            @break
                                                        @case('Masbate PO')
                                                            {{ '(' . ($indicator->Masbate_target  == 0 ? 'Actual' : $indicator->Masbate_target ) . ') ' . $indicator->measures }}
                                                            @break
                                                        @case('Sorsogon PO')
                                                            {{ '(' . ($indicator->Sorsogon_target == 0 ? 'Actual' : $indicator->Sorsogon_target) . ') ' . $indicator->measures }}
                                                            @break
                                                        @default
                                                            {{ '(' . ($indicator->target == 0 ? 'Actual' : $indicator->target) . ') ' . $indicator->measures }}
                                                    @endswitch
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @if(empty($filteredDivisionIds))
                                            <!-- If no division is filtered, show the target and measures -->
                                            {{ number_format($indicator->alloted_budget, 2) }}
                                        @else
                                            <!-- If division is filtered, show the specific budget based on division -->
                                            @foreach($division_ids as $divisionId)
                                                @php
                                                    $division = \App\Models\Division::find($divisionId);
                                                    $showDivision = in_array($divisionId, $filteredDivisionIds);

                                                @endphp

                                                @if($showDivision && $division)
                                                    @switch($division->division_name)
                                                        @case('Albay PO')
                                                            {{ number_format($indicator->Albay_budget, 2) }}
                                                            @break
                                                        @case('Camarines Norte PO')
                                                            {{ number_format($indicator->Camarines_Norte_budget, 2) }}
                                                            @break
                                                        @case('Camarines Sur PO')
                                                            {{ number_format($indicator->Camarines_Sur_budget, 2) }}
                                                            @break
                                                        @case('Catanduanes PO')
                                                            {{ number_format($indicator->Catanduanes_budget, 2) }}
                                                            @break
                                                        @case('Masbate PO')
                                                            {{ number_format($indicator->Masbate_budget, 2) }}
                                                            @break
                                                        @case('Sorsogon PO')
                                                            {{ number_format($indicator->Sorsogon_budget, 2) }}
                                                            @break
                                                        @default
                                                            {{ number_format($indicator->alloted_budget, 2) }}
                                                    @endswitch
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($division_ids as $divisionId)
                                            @php
                                                $division = \App\Models\Division::find($divisionId);
                                                $showDivision = empty($filteredDivisionIds) || in_array($divisionId, $filteredDivisionIds);
                                            @endphp

                                            @if($showDivision && $division)
                                                {{ $division->division_name }},<br>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>

                                        @if(empty($filteredDivisionIds))
                                            <!-- If no division is filtered, show the target and measures -->
                                            {{ '(' .($entriesForIndicator->sum('total_accomplishment')) . ')' . ' ' . $indicator->measures }}
                                        @else
                                            <!-- If division is filtered, show the specific budget based on division -->
                                            @foreach($division_ids as $divisionId)
                                                @php
                                                    $division = \App\Models\Division::find($divisionId);
                                                    $showDivision = in_array($divisionId, $filteredDivisionIds);
                                                @endphp

                                                @if($showDivision && $division)
                                                    @if($division->division_name === 'Albay PO')
                                                    {{ '(' .($entriesForIndicator->sum('Albay_accomplishment')) . ')' . ' ' . $indicator->measures }}

                                                    @elseif($division->division_name === 'Camarines Norte PO')
                                                    {{ '(' . ($entriesForIndicator->sum('Camarines_Norte_accomplishment'))  . ')' . ' ' . $indicator->measures }}

                                                    @elseif($division->division_name === 'Camarines Sur PO')
                                                    {{ '(' . ($entriesForIndicator->sum('Camarines_Sur_accomplishment')) . ')' . ' ' . $indicator->measures }}

                                                    @elseif($division->division_name === 'Catanduanes PO')
                                                    {{ '(' . ($entriesForIndicator->sum('Catanduanes_accomplishment'))   . ')' . ' ' . $indicator->measures }}

                                                    @elseif($division->division_name === 'Masbate PO')
                                                    {{ '(' . ($entriesForIndicator->sum('Masbate_accomplishment') ) . ')' . ' ' . $indicator->measures }}

                                                    @elseif($division->division_name === 'Sorsogon PO')
                                                    {{ '(' . ($entriesForIndicator->sum('Sorsogon_accomplishment'))   . ')' . ' ' . $indicator->measures }}

                                                    @else
                                                    {{ number_format($entriesForIndicator->sum('total_accomplishment')) . ' ' . $indicator->measures }}
                                                    @endif
                                                @endif
                                            @endforeach
                                        @endif
                                        {{-- @foreach($groupedEntries as $month => $monthlyEntries)
                                            <strong>{{ \Carbon\Carbon::create()->month($month)->format('F') }}:</strong><br>
                                            @foreach($monthlyEntries as $entry)
                                                {{ $entry->accomplishment }}<br><br>
                                            @endforeach
                                        @endforeach --}}
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>

                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                @else
                    <p>No Core outcomes available.</p>
                @endif

                <tr>
                    <td colspan="10" style="background-color: #8ccdf0; font-weight: bold; text-align: left;">
                        Non Core
                    </td>
                </tr>

                @if(!empty($groupedOutcomes['Non Core']))
                    @foreach ($groupedOutcomes['Non Core'] as $outcome)
                        @php
                            $hasIndicators = count($outcome->successIndicators) > 0;
                        @endphp
                        @if(!$hasIndicators)
                            <tr class="no-indicators">
                                <td colspan="1">
                                    {{ $outcome->organizational_outcome }}
                                </td>
                                <td colspan="9"></td>
                            </tr>

                        @else
                            @foreach($outcome->successIndicators as $index => $indicator)
                                    @php
                                        $division_ids = json_decode($indicator->division_id);
                                        $filteredDivisionIds = $divisionIds ?? []; // The filtered division IDs from the request
                                        $entriesForIndicator = $entries[$indicator->id] ?? collect();
                                    @endphp

                                    @if($index > 0 && $index % 10 === 0)
                                    <!-- Add a page break every 10 rows for large tables -->
                                    <tr class="page-break"><td colspan="10"></td></tr>
                                    @endif
                                <tr>
                                    
                                    @if($index === 0)
                                    {{-- <td></td> --}}
                                    <td rowspan="{{ count($outcome->successIndicators) }}">{{ $outcome->organizational_outcome }}</td>

                                    @endif
                                    <td>
                                        @if(empty($filteredDivisionIds))
                                            <!-- If no division is filtered, show the target and measures -->
                                            {{ '(' . ($indicator->target == 0 ? 'Actual' : $indicator->target) . ') ' . $indicator->measures }}
                                        @else
                                            <!-- If division is filtered, show the specific budget based on division -->
                                            @foreach($division_ids as $divisionId)
                                                @php
                                                    $division = \App\Models\Division::find($divisionId);
                                                    $showDivision = in_array($divisionId, $filteredDivisionIds);
                                                @endphp

                                                @if($showDivision && $division)
                                                    @switch($division->division_name)
                                                        @case('Albay PO')
                                                            {{ '(' . ($indicator->Albay_target  == 0 ? 'Actual' : $indicator->Albay_target ) . ') ' . $indicator->measures }}
                                                            @break
                                                        @case('Camarines Norte PO')
                                                            {{ '(' . ($indicator->Camarines_Norte_target == 0 ? 'Actual' : $indicator->Camarines_Norte_target) . ') ' . $indicator->measures }}
                                                            @break
                                                        @case('Camarines Sur PO')
                                                            {{ '(' . ($indicator->Camarines_Sur_target == 0 ? 'Actual' :  $indicator->Camarines_Sur_target) . ') ' . $indicator->measures }}
                                                            @break
                                                        @case('Catanduanes PO')
                                                            {{ '(' . ($indicator->Catanduanes_target == 0 ? 'Actual' : $indicator->Catanduanes_target) . ') ' . $indicator->measures }}
                                                            @break
                                                        @case('Masbate PO')
                                                            {{ '(' . ($indicator->Masbate_target  == 0 ? 'Actual' : $indicator->Masbate_target ) . ') ' . $indicator->measures }}
                                                            @break
                                                        @case('Sorsogon PO')
                                                            {{ '(' . ($indicator->Sorsogon_target == 0 ? 'Actual' : $indicator->Sorsogon_target) . ') ' . $indicator->measures }}
                                                            @break
                                                        @default
                                                            {{ '(' . ($indicator->target == 0 ? 'Actual' : $indicator->target) . ') ' . $indicator->measures }}
                                                    @endswitch
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @if(empty($filteredDivisionIds))
                                            <!-- If no division is filtered, show the target and measures -->
                                            {{ number_format($indicator->alloted_budget, 2) }}
                                        @else
                                            <!-- If division is filtered, show the specific budget based on division -->
                                            @foreach($division_ids as $divisionId)
                                                @php
                                                    $division = \App\Models\Division::find($divisionId);
                                                    $showDivision = in_array($divisionId, $filteredDivisionIds);

                                                @endphp

                                                @if($showDivision && $division)
                                                    @switch($division->division_name)
                                                        @case('Albay PO')
                                                            {{ number_format($indicator->Albay_budget, 2) }}
                                                            @break
                                                        @case('Camarines Norte PO')
                                                            {{ number_format($indicator->Camarines_Norte_budget, 2) }}
                                                            @break
                                                        @case('Camarines Sur PO')
                                                            {{ number_format($indicator->Camarines_Sur_budget, 2) }}
                                                            @break
                                                        @case('Catanduanes PO')
                                                            {{ number_format($indicator->Catanduanes_budget, 2) }}
                                                            @break
                                                        @case('Masbate PO')
                                                            {{ number_format($indicator->Masbate_budget, 2) }}
                                                            @break
                                                        @case('Sorsogon PO')
                                                            {{ number_format($indicator->Sorsogon_budget, 2) }}
                                                            @break
                                                        @default
                                                            {{ number_format($indicator->alloted_budget, 2) }}
                                                    @endswitch
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($division_ids as $divisionId)
                                            @php
                                                $division = \App\Models\Division::find($divisionId);
                                                $showDivision = empty($filteredDivisionIds) || in_array($divisionId, $filteredDivisionIds);
                                            @endphp

                                            @if($showDivision && $division)
                                                {{ $division->division_name }},<br>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>

                                        @if(empty($filteredDivisionIds))
                                            <!-- If no division is filtered, show the target and measures -->
                                            {{ '(' .($entriesForIndicator->sum('total_accomplishment')) . ')' . ' ' . $indicator->measures }}
                                        @else
                                            <!-- If division is filtered, show the specific budget based on division -->
                                            @foreach($division_ids as $divisionId)
                                                @php
                                                    $division = \App\Models\Division::find($divisionId);
                                                    $showDivision = in_array($divisionId, $filteredDivisionIds);
                                                @endphp

                                                @if($showDivision && $division)
                                                    @if($division->division_name === 'Albay PO')
                                                    {{ '(' .($entriesForIndicator->sum('Albay_accomplishment')) . ')' . ' ' . $indicator->measures }}

                                                    @elseif($division->division_name === 'Camarines Norte PO')
                                                    {{ '(' . ($entriesForIndicator->sum('Camarines_Norte_accomplishment'))  . ')' . ' ' . $indicator->measures }}

                                                    @elseif($division->division_name === 'Camarines Sur PO')
                                                    {{ '(' . ($entriesForIndicator->sum('Camarines_Sur_accomplishment')) . ')' . ' ' . $indicator->measures }}

                                                    @elseif($division->division_name === 'Catanduanes PO')
                                                    {{ '(' . ($entriesForIndicator->sum('Catanduanes_accomplishment'))   . ')' . ' ' . $indicator->measures }}

                                                    @elseif($division->division_name === 'Masbate PO')
                                                    {{ '(' . ($entriesForIndicator->sum('Masbate_accomplishment') ) . ')' . ' ' . $indicator->measures }}

                                                    @elseif($division->division_name === 'Sorsogon PO')
                                                    {{ '(' . ($entriesForIndicator->sum('Sorsogon_accomplishment'))   . ')' . ' ' . $indicator->measures }}

                                                    @else
                                                    {{ number_format($entriesForIndicator->sum('total_accomplishment')) . ' ' . $indicator->measures }}
                                                    @endif
                                                @endif
                                            @endforeach
                                        @endif
                                        {{-- @foreach($groupedEntries as $month => $monthlyEntries)
                                            <strong>{{ \Carbon\Carbon::create()->month($month)->format('F') }}:</strong><br>
                                            @foreach($monthlyEntries as $entry)
                                                {{ $entry->accomplishment }}<br><br>
                                            @endforeach
                                        @endforeach --}}
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>

                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                @else
                    <p>No Non Core outcomes available.</p>
                @endif
               
            
            </tbody>
        </table>
    </div>
    <div class="footer">
        <span class="page-number"></span>
    </div>

</body>
</html>
