@extends('layouts.admin')

@section('content')
    <div class="panel">
        <h3>Create category</h3>
        <p>New categories appear in the storefront and API when active.</p>
        <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" style="margin-top: 18px;">
            @include('admin.categories._form', ['submitLabel' => 'Create category'])
        </form>
    </div>
@endsection
