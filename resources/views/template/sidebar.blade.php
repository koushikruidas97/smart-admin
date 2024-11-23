<div class="col-sm-3 sidebar-bg">
    <div class="sidebar-main">
        <div class="account-sec">
            <div class="account-icon">
                <img src="{{ asset('backend/images/profile.png') }}" alt="">
            </div>
            <div class="account-text">
                <span>Koushik Ruidas</span>
                <small>ID : AB3025</small>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul>
                @php
                $pageform = Request::segment(1); // Get the first segment of the URL
                @endphp
                <li class="{{ $pageform === 'dashboard' ? 'active' : '' }}">
                    <a href="javascript:void(0)" onclick="window.location='/Employee'">
                        <i class="fas fa-tachometer-alt"></i>
                        Employee Master
                    </a>
                </li>
                <li class="{{ $pageform === 'banners' ? 'active' : '' }}">
                    <a href="javascript:void(0)" onclick="window.location='/lead-master'">
                        <i class="fas fa-images"></i>
                        Lead Master
                    </a>
                </li>
                <li class="{{ $pageform === 'gallery' ? 'active' : '' }}">
                    <a href="javascript:void(0)" onclick="window.location='/folowup'">
                        <i class="fas fa-images"></i>
                        Follow up Master
                    </a>
                </li>

            </ul>

        </div>
    </div>
</div>