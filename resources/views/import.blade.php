@extends('theme')


@section('content')



@php
$leads = App\Models\Lead::all();
$leadCount = $leads->count();
@endphp

<p>Total Leads: {{ $leadCount }}</p>

@php
    $lists = App\Models\Leadlist::orderBy('id', 'desc')->get();
@endphp

{{-- <form action="/import" method="post" enctype="multipart/form-data">
    @csrf

    <select type="select" class="form-control mb-3" name="list_id">
        @foreach($lists as $list)
            <option value="{{ $list->id }}">{{ $list->name }}</option>
        @endforeach
    </select>

    <input type="file" name="file" id="csvFile" accept=".csv" class="form-control mb-3">
    <div id="progress"></div>

    <input type="submit" value="Import Now" name="Import" class="btn btn-secondary">
</form>

@if(session()->has('success'))
<p class="mt-5" style="color: green;">{{ session('success') }}l</p>
@endif --}}


<form id="uploadForm" enctype="multipart/form-data">
    <select type="select" class="form-control mb-3" name="list_id">
        @foreach($lists as $list)
            <option value="{{ $list->id }}">{{ $list->name }}</option>
        @endforeach
    </select>
    <input type="file" id="csvFile" accept=".csv">
    <button type="submit">Upload</button>
</form>
<div id="progress"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
<script>
    $(document).ready(function() {
        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            
            const file = $('#csvFile')[0].files[0];
            const listId = $('select[name="list_id"]').val();  // Get the selected list_id

            if (!file) {
                alert('Please select a CSV file');
                return;
            }

            Papa.parse(file, {
                header: true,
                complete: function(results) {
                    const data = results.data;
                    processInChunks(data, 1000, listId);  // Pass listId to processInChunks
                }
            });
        });

        function processInChunks(data, chunkSize, listId) {
            let currentIndex = 0;

            function processChunk() {
                const chunk = data.slice(currentIndex, currentIndex + chunkSize);
                if (chunk.length === 0) return;

                // Add the list_id to each chunk being sent
                const payload = {
                    list_id: listId,
                    data: chunk
                };

                $.ajax({
                    url: '/api/ajax-lead-import',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    success: function(response) {
                        console.log('Processed chunk:', response);
                        currentIndex += chunkSize;

                        // Update progress
                        $('#progress').text(`Processed ${Math.min(currentIndex, data.length)} of ${data.length} records`);

                        if (currentIndex < data.length) {
                            setTimeout(processChunk, 1000);
                        }
                    },
                    error: function(err) {
                        console.error('Error processing chunk:', err);
                    }
                });
            }

            processChunk();
        }
    });
</script>

@endsection
