@extends('layouts.admin')

@section('content')
    <div class="panel">
        <h3>Create menu item</h3>
        <p>Add a new fast-food menu item for the storefront and APIs.</p>
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" style="margin-top: 18px;">
            @include('admin.products._form', ['submitLabel' => 'Create product'])
        </form>
    </div>
@endsection
