<nav class="navbar navbar-light navbar-expand shadow mb-4 topbar static-top">
    <div class="container-fluid">
        <button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button" style="margin-top: 16px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-align-left" style="color: rgb(62,66,75);font-size: 20px;">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <line x1="4" y1="6" x2="20" y2="6"></line>
                <line x1="4" y1="12" x2="14" y2="12"></line>
                <line x1="4" y1="18" x2="18" y2="18"></line>
            </svg>
        </button>
        <ul class="navbar-nav flex-nowrap ms-auto">
            <div class="d-none d-sm-block topbar-divider"></div>
            <li class="nav-item dropdown no-arrow"></li>
            <li class="nav-item dropdown no-arrow">
                <div class="nav-item dropdown no-arrow">
                    <a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#">
                        <span class="d-none d-lg-inline me-2 text-gray-600 small"><?php  //echo $nome; ?></span>
                        <i class="fas fa-user-circle" style="font-size: 25px;color: rgb(82,82,84);"></i>
                    </a>
                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in">
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?php echo $logout; ?>">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>Sair
                        </a>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</nav>