@extends('layouts.admin')

@section('content')
    <div class="panel">
        <div class="toolbar" style="justify-content: space-between; margin-bottom: 16px;">
            <div>
                <h3>Categories</h3>
                <p>Manage the public menu grouping structure.</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="button">New category</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td class="meta">{{ $category->slug }}</td>
                        <td>{{ $category->products_count }}</td>
                        <td>
                            <span @class(['badge', 'good' => $category->is_active])>
                                {{ $category->is_active ? 'Active' : 'Hidden' }}
                            </span>
                        </td>
                        <td>
                            <div class="toolbar">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="button secondary">Edit</a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
