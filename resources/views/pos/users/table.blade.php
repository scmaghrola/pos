<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Name</th>
                <th class="text-center">Email</th>
                <th class="text-center">Roles</th>
                <th class="text-center">Created</th>
                <th class="text-center">Actions</th>
                @role('Super Admin')
                    <th class="text-center">Set Permission</th>
                @endrole
            </tr>
        </thead>

        <tbody>
            @forelse($users as $user)
                <tr>
                    <td class="text-center">{{ $user->id }}</td>
                    <td class="text-center">{{ $user->name }}</td>
                    <td class="text-center">{{ $user->email }}</td>
                    <td class="text-center">
                        @if ($user->hasRole('Super Admin'))
                            <span class="badge bg-warning text-dark">Super Admin</span>
                        @else
                            @php $userRoles = $user->getRoleNames(); @endphp
                            @if ($userRoles->isNotEmpty())
                                @foreach ($userRoles as $role)
                                    <span class="text-dark">{{ $role }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">No Role</span>
                            @endif
                        @endif
                    </td>
                    <td class="text-center">{{ $user->created_at->diffForHumans() }}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary view-user-btn"
                            data-bs-toggle="modal" data-bs-target="#viewUserModal"
                            data-id="{{ $user->id }}">View</button>

                        @if ($user->hasRole('Super Admin'))
                            <button class="btn btn-sm btn-outline-secondary" disabled>Edit</button>
                            <button class="btn btn-sm btn-outline-secondary" disabled>Delete</button>
                        @else
                            <a href="{{ route('users.edit', ['user' => $user->id, 'action' => 'edit']) }}"
                                class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('users.destroy', $user) }}" method="POST"
                                class="d-inline-block confirm-delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        @endif
                    </td>
                    @role('Super Admin')
                        <td class="text-center">
                            @if ($user->hasRole('Super Admin'))
                                <button class="btn btn-sm btn-outline-secondary" disabled>Set Permission</button>
                            @else
                                <a href="{{ route('users.permissions.edit', ['id' => $user->id]) }}"
                                    class="btn btn-sm btn-outline-success">Set Permission</a>
                            @endif
                        </td>
                    @endrole

                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-between align-items-center flex-wrap mt-3">
        <div class="mb-2 mb-md-0">
            <small class="text-muted">
                Showing <strong>{{ $users->firstItem() ?? 0 }}</strong>
                to <strong>{{ $users->lastItem() ?? 0 }}</strong>
                of <strong>{{ $users->total() ?? 0 }}</strong> users
            </small>
        </div>
        <div>
            {!! $users->appends(request()->query())->links('pagination::bootstrap-5') !!}
        </div>
    </div>
</div>
