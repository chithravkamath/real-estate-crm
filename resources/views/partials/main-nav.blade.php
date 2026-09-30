<ul class="nav navbar-nav">
  @if(auth()->user() && auth()->user()->role === 'admin')
    <li class="{{ request()->is('dashboard') ? 'active' : '' }}"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
    <li class="{{ (request()->is('properties*') || request()->is('property-details*')) ? 'active' : '' }}"><a href="{{ url('/properties') }}">Property Management</a></li>
    <li class="{{ (request()->is('clients*') || request()->is('client-details*')) ? 'active' : '' }}"><a href="{{ url('/clients') }}">Clients</a></li>
    <li class="{{ (request()->is('leads*') || request()->is('lead-details*')) ? 'active' : '' }}"><a href="{{ url('/leads') }}">Leads</a></li>
    <li class="{{ (request()->is('site-visits*') || request()->is('visit*')) ? 'active' : '' }}"><a href="{{ url('/site-visits') }}">Site Visits</a></li>
    <li class="{{ (request()->is('deals*') || request()->is('deal-details*') || request()->is('deal-update*') || request()->is('negotiations*') || request()->is('documents*') || request()->is('upload-document*')) ? 'active' : '' }}"><a href="{{ url('/deals') }}">Deals / Sales</a></li>
    <li class="{{ (request()->is('billing*') || request()->is('billings*')) ? 'active' : '' }}"><a href="{{ url('/billing') }}">Billing & Commission</a></li>
    <li class="{{ request()->is('reports*') ? 'active' : '' }}"><a href="{{ url('/reports') }}">Reports</a></li>
    <li class="{{ request()->is('users*') ? 'active' : '' }}"><a href="{{ url('/users') }}">Users</a></li>
    <li class="{{ request()->is('audit-logs*') ? 'active' : '' }}"><a href="{{ url('/audit-logs') }}">Audit Logs</a></li>
  @elseif(auth()->user() && auth()->user()->role === 'agent')
    <li class="{{ request()->is('dashboard') ? 'active' : '' }}"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
    <li class="{{ (request()->is('clients*') || request()->is('client-details*')) ? 'active' : '' }}"><a href="{{ url('/clients') }}">Clients</a></li>
    <li class="{{ (request()->is('leads*') || request()->is('lead-details*')) ? 'active' : '' }}"><a href="{{ url('/leads') }}">Leads</a></li>
    <li class="{{ (request()->is('site-visits*') || request()->is('visit*')) ? 'active' : '' }}"><a href="{{ url('/site-visits') }}">Site Visits</a></li>
    <li class="{{ (request()->is('deals*') || request()->is('deal-details*') || request()->is('deal-update*') || request()->is('negotiations*') || request()->is('documents*') || request()->is('upload-document*')) ? 'active' : '' }}"><a href="{{ url('/deals') }}">Deals / Sales</a></li>
    <li class="{{ request()->is('reminders*') ? 'active' : '' }}"><a href="{{ url('/reminders') }}">Reminders</a></li>
  @elseif(auth()->user() && auth()->user()->role === 'accountant')
    <li class="{{ request()->is('dashboard') ? 'active' : '' }}"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
    <li class="{{ (request()->is('billing*') || request()->is('billings*')) ? 'active' : '' }}"><a href="{{ url('/billing') }}">Billing & Commission</a></li>
    <li class="{{ request()->is('reports*') ? 'active' : '' }}"><a href="{{ url('/reports') }}">Reports</a></li>
  @endif
</ul>
