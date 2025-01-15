<div class="wrapper d-flex align-items-stretch">
    <nav id="sidebar">
        <div class="div-nav-logo d-flex justify-content-center align-items-center">
            <img src="{{ asset('img/logo.png') }}" class="nav-logo-container" alt="Login">
        </div>
        <div class="custom-menu">
            <button type="button" id="sidebarCollapse" class="btn btn-primary">
                <i class="fa fa-bars"></i>
                <span class="sr-only">Toggle Menu</span>
            </button>
        </div>
        <div class="p-4 pt-5">
            {{-- <h1 class=""><a href="" class="logo">BUGS</a></h1> --}}
            <ul class="list-unstyled components mb-5">
                <li class="">
                    <a href="#">Dashboard</a>
                </li>
                <li>
                    <a href="#">Notification <span class="badge badge-pill badge-success">10</span></a>
                </li>
                <li>
                    <a href="#homeSubmenu" data-toggle="collapse" aria-expanded="false" class=""><small style="margin-right: 30%; font-size: 16px;">Transactions</small><i class="bi bi-caret-down-fill flex-end"></i></a>
                    <ul class="collapse list-unstyled" id="homeSubmenu">
                    <li>
                        <a href="#">Honorarium</a>
                    </li>
                    <li>
                        <a href="#">New Entries</a>
                    </li>
                    <li>
                        <a href="#">On Queue</a>
                    </li>
                    <li>
                        <a href="#">Pending</a>
                    </li>
                    <li>
                        <a href="#">Tracking</a>
                    </li>
                    </ul>
                </li>
                <li>
                    <a href="#">Transaction History</a>
                </li>
                <li>
                    <a href="#">Users</a>
                </li>
                <li>
                    <a href="#">System Users</a>
                </li>
            </ul>
      </div>
    </nav>
    
    
    