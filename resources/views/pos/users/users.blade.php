@extends('pos.layout.layout')

@section('title', 'Users')

@section('content')

    <div class="container" id="userTable">
        @include('pos.users.table', ['users' => $users])
    </div>



    <!-- View User Modal -->
    <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="viewUserModalLabel">User Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th style="width: 30%;">Name:</th>
                            <td id="viewUserName"></td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td id="viewUserEmail"></td>
                        </tr>
                        <tr>
                            <th>Roles:</th>
                            <td id="viewUserRoles"></td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td id="viewUserCreated"></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {

                $('.view-user-btn').click(function() {
                    var userId = $(this).data('id');

                    $.ajax({
                        url: '{{ route('users.show', ':id') }}'.replace(':id', userId),
                        type: 'GET',
                        dataType: 'json',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(user) {
                            $('#viewUserName').text(user.name);
                            $('#viewUserEmail').text(user.email);
                            $('#viewUserRoles').text(user.roles.length ? user.roles.join(', ') :
                                'No Role');
                            $('#viewUserCreated').text(user.created_at);
                        },
                        error: function(xhr) {
                            alert('Failed to fetch user data: ' + xhr.status + ' ' + xhr
                                .statusText);
                        }
                    });
                });
            });
        </script>

        <script>
            $(document).ready(function() {

                function fetchUsers(url) {
                    $.ajax({
                        url: url,
                        type: "GET",
                        dataType: "html",
                        success: function(data) {
                            $('#userTable').html(data);
                        },
                        error: function(xhr) {
                            alert('Failed to load users.');
                        }
                    });
                }

                // Pagination click
                $(document).on('click', '.pagination a', function(e) {
                    e.preventDefault();
                    let url = $(this).attr('href');
                    fetchUsers(url);
                });

                // Search form submit
                $('form').on('submit', function(e) {
                    e.preventDefault();
                    let url = $(this).attr('action') + '?' + $(this).serialize();
                    fetchUsers(url);
                });

            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.confirm-delete-form').forEach(function(form) {
                    form.addEventListener('submit', function(e) {
                        if (!confirm(
                                'Are you sure you want to delete this user? This action cannot be undone.'
                            )) {
                            e.preventDefault();
                        }
                    });
                });
            });
        </script>
    @endpush

@endsection
