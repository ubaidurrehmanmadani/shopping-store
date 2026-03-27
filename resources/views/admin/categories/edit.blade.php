@extends('layouts.admin')

@section('content')
    <div class="panel">
        <h3>Edit category</h3>
        <p>Update storefront grouping, order, and visibility.</p>
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data" style="margin-top: 18px;">
            @include('admin.categories._form', ['submitLabel' => 'Save changes'])
        </form>
    </div>
@endsection
