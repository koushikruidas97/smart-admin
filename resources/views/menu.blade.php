@include('template.header')
<style>
    .menu {
        list-style: none;
        padding: 0;
    }

    .menu-item {
        margin: 5px 0;
        padding: 10px;
        border: 1px solid #ccc;
        cursor: move;
    }

    .nested {
        margin-left: 20px;
    }
</style>
<?php
$formFields = [
    [
        'id' => 'parentMenu',
        'label' => 'Parent Menu',
        'name' => 'parent_menu',
        'type' => 'dropdown',
        'options' => []
    ],
    [
        'id' => 'MenuName',
        'label' => 'Menu Name',
        'name' => 'menu',
        'type' => 'text'
    ],
    [
        'id' => 'Link',
        'label' => 'Link',
        'name' => 'link',
        'type' => 'text'
    ]
];

// foreach ($data['items'] as $menu) {
//     $formFields[0]['options'][] = [
//         'value' => $menu->id,
//         'label' => $menu->menu
//     ];
// }
?>

<div class="container-fluid">
    <div class="row">
        @include('template.sidebar')
        <div class="col-sm-9">
            <div class="ban">
                <div class="left-ban">
                    <div class="left-ban-text">
                        <p><i class="fas fa-bars"></i> Menus</p>
                    </div>
                </div>
                <div class="right-ban">
                    <div class="right-ban-text">
                        <p>
                            <span class="parent-page" onclick="window.location.href='/dashboard'">Dashboard</span>
                            <span class="slash">/</span>
                            <span>Menus</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 mt-5 text-end">
                <a href="javascript:void(0)" class="btn btn-primary" onclick="menu(null, '{{ base64_encode(json_encode($formFields)) }}', null, '{{ request()->segment(1) }}')">
                    <i class="fas fa-solid fa-circle-plus"></i> Add Menu
                </a>
            </div>
            <div class="col-sm-12 mt-5">
                <ul class="menu">
                    @foreach ($menus as $menu)
                    <li>{{ $menu->menu}}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @include('template.footer')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <script>
    var el = document.getElementById('menu');
    var sortable = Sortable.create(el, {
        animation: 150,
        onEnd: function (evt) {
            // Handle the drag end event if needed
        }
    });

    $('#save-order').on('click', function() {
        var menus = [];
        $('#menu .menu-item').each(function(index) {
            menus.push({
                id: $(this).data('id'),
                sort_order: index + 1
            });
        });

        $.ajax({
            url: '/menus/update-order',
            method: 'POST',
            data: {
                menus: menus,
                _token: '{{ csrf_token() }}' // Add CSRF token for security
            },
            success: function(response) {
                alert('Order saved successfully!');
            }
        });
    });
</script>
</div>