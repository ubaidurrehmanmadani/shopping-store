@extends('layouts.admin')

@section('content')
    <div class="panel">
        <h3>Edit menu item</h3>
        <p>Adjust pricing, category mapping, image, and featured status.</p>
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" style="margin-top: 18px;">
            @include('admin.products._form', ['submitLabel' => 'Save changes'])
        </form>
    </div>
@endsection
