@extends('pos.layout.layout')
@section('content')
    <div class="container mt-4">
        <h4>Manage Permissions for <strong>{{ $user->name }}</strong></h4>
        <hr>

        <form id="permissionForm">
            @csrf
            <div class="row">
                @foreach ($permissions as $module => $perms)
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                {{ ucfirst($module) }}
                            </div>
                            <div class="card-body">
                                @foreach ($perms as $perm)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="{{ $perm->name }}" id="perm_{{ $perm->id }}"
                                            {{ $user->hasPermissionTo($perm->name) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm_{{ $perm->id }}">
                                            {{ ucfirst(explode('.', $perm->name)[1]) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" id="savePermissions" class="btn btn-success mt-3">Save Changes</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">Cancel</a>
        </form>
    </div>

    <script>
        document.getElementById('savePermissions').addEventListener('click', function() {
            let form = document.getElementById('permissionForm');
            let formData = new FormData(form);

            fetch("{{ route('users.permissions.update', $user->id) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.href = "{{ route('users.index') }}";
                    }
                })
                .catch(err => console.error(err));
        });
    </script>
@endsection
