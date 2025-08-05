@extends('layouts.app')

@section('content')
    <div class="main-content">
        <script>
            window.location.href = "{{ route('staff.medicine-pickups') }}";
        </script>
    </div>
@endsection
