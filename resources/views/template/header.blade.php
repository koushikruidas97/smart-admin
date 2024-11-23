<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('backend/css/style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
    <!-- Include Cropper.js CSS -->
    <link href="https://unpkg.com/cropperjs/dist/cropper.css" rel="stylesheet">
</head>

<body>
    <!-- Header Design -->
    <div class="header-sec">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-3 bdr-bg">
                    <div class="logo">
                        <img src="{{ asset('backend/images/logo.png') }}" alt="">
                    </div>
                </div>
                <div class="col-sm-9 profile-sec">
                    <div class="color-heading">CRM Portal</div>
                    <div class="user-icon">
                        <div class="icon">
                            <img src="{{ asset('backend/images/profile.png') }}" alt="">
                        </div>
                        <div class="user-text">Koushik Ruidas
                            <span>Online</span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>