@props(['headers'])

<div class="table-responsive mt-4">
    <table class="w-100 text-center">
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th class="text-warning">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody id="table-list">
            {{ $slot }}
        </tbody>
    </table>  
</div>
