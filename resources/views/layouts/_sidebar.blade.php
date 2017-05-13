
<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li class="header">MENU PRINCIPAL

            <li class="{{ str_contains(Request::path(), "trips") ? 'active' : '' }}">
                <a href="/trips"><i class="fa fa-search"></i>Pesquisas</a>
            </li>

            <li class="{{ str_contains(Request::path(), "users") ? 'active' : '' }}">
                <a href="/users"><i class="fa fa-users"></i>Usuários</a>
            </li>

            <li><a href="/logout"><i class="glyphicon glyphicon-log-out"></i> Sair</a></li>
        </ul>
    </section>
</aside>