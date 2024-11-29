@extends('theme')


@section('content')

<style>
div#progress {
    margin-top: 20px;
}
</style>


<form id="uploadForm" enctype="multipart/form-data">
    <input type="hidden" name="list_id" value="{{ $list_id }}">
    <input type="file" id="csvFile" accept=".csv">
    <button class="btn btn-secondary" type="submit">Upload</button>
</form>
<div id="progress"></div>

<div class="mt-5">
    <a href="{{ route('show.list', $list_id) }}">Go Back</a>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
<script>
    $(document).ready(function() {
        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            $('#progress').text('Processing...');
            
            const file = $('#csvFile')[0].files[0];
            const listId = $('input[name="list_id"]').val();  // Get the selected list_id

            if (!file) {
                alert('Please select a CSV file');
                return;
            }

            Papa.parse(file, {
                header: true,
                skipEmptyLines: true,  // Skip empty lines
                complete: function(results) {
                    const data = results.data;
                    if (data.length === 0) { 
                        alert('No data found in the CSV file');
                        return;
                    }
                    processInChunks(data, 500, listId);  // Pass listId to processInChunks
                }
            });
        });

        function processInChunks(data, chunkSize, listId) {
            let currentIndex = 0;
            const totalRecords = data.length;  // Total records excluding header

            function processChunk() {
                const chunk = data.slice(currentIndex, currentIndex + chunkSize);
                if (chunk.length === 0) return;

                // Add the list_id to each chunk being sent
                const payload = {
                    list_id: listId,
                    data: chunk
                };

                $.ajax({
                    url: '/api/uplaod-leads',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    success: function(response) {
                        console.log('Processed chunk:', response);
                        currentIndex += chunkSize;

                        // Update progress
                        $('#progress').text(`Processed ${Math.min(currentIndex, totalRecords)} of ${totalRecords} records`);

                        if (currentIndex < totalRecords) {
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
