@include('template.header')
<?php
// Dynamically populate form fields using the passed $menuData
$formFields = [
    [
        'id' => 'menuName',
        'label' => 'Menu Name',
        'name' => 'menu_id',
        'type' => 'dropdown',
        'options' => []
    ],
    [
        'id' => 'title',
        'label' => 'Title',
        'name' => 'title',
        'type' => 'text'
    ],
    [
        'id' => 'description',
        'label' => 'Description',
        'name' => 'description',
        'type' => 'textarea'
    ],
    [
        'id' => 'image',
        'label' => 'Image',
        'name' => 'image',
        'type' => 'file'
    ]
];

foreach ($menuData as $menu) {
    $formFields[0]['options'][] = [
        'value' => $menu->id, // Assuming 'id' is the menu's unique identifier
        'label' => $menu->name // Assuming 'name' is the display name of the menu
    ];
}
?>

@php
$pageform = Request::segment(1); // Get the first segment of the URL
@endphp

<!-- main-wrap -->
<div class="container-fluid">
    <div class="row">
        @include('template.sidebar')

        <div class="col-sm-9">
            <div class="ban">
                <div class="left-ban">
                    <div class="left-ban-text">
                        <p><i class="fas fa-images"></i> {{ucfirst($pageform)}}</p>
                    </div>
                </div>
                <div class="right-ban">
                    <div class="right-ban-text">
                        <p>
                            <span class="parent-page" onclick="window.location.href='/dashboard'">Dashboard</span>
                            <span class="slash">/</span>
                            <span>{{ucfirst($pageform)}}</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 mt-5 text-end">
                <a href="javascript:void(0)" class="btn btn-primary" onclick="menu(null,'<?= base64_encode(json_encode($formFields)) ?>',null,'{{$pageform}}')">
                    <i class="fas fa-solid fa-circle-plus"></i> Add Banner
                </a>
            </div>
            <div class="col-sm-12 mt-3">
                <div class="card br-primary">
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Menu Name</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                <tr id="row-{{ $item->id }}">
                                    <td>
                                        @foreach ($menuData as $menu)
                                        @if ($item->menu_id == $menu->id)
                                        {{ Str::limit($menu->name,20) }}
                                        @endif
                                        @endforeach
                                    </td>
                                    <td>{{ Str::limit($item->title,20) }}</td>
                                    <td>{{ Str::limit($item->description,20) }}</td>
                                    <td>
                                        @if ($item->image)
                                        <a href="{{ asset('storage/'.$item->image) }}" data-fancybox="gallery" class="image-link">
                                            <img src="{{ asset('storage/'.$item->image) }}" class="w-10" alt="Image">
                                        </a>
                                        @endif
                                    </td>
                                    <td class="action-td">
                                        <div class="d-flex g-3">
                                            <a href="javascript:void(0)" onclick="menu('{{ base64_encode(json_encode($item)) }}', '{{ base64_encode(json_encode($formFields)) }}',null,'{{$pageform}}')" class="eact btn btn-primary">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <a href="javascript:void(0)" class="eact btn btn-danger" onclick="deleteModal('{{ $item->id }}','{{$pageform}}')">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Pagination Links -->
                <div class="d-flex justify-content-end mt-3" id="pagination">
                    {{ $items->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
    @include('template.footer')
    <script>
        $(document).ready(function() {
            $('[data-fancybox="gallery"]').fancybox({
                // Optional customization
                buttons: [
                    "zoom",
                    "close"
                ],
                // Add other settings if necessary
            });
        });
    </script> 