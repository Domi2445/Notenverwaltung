{{--
    Dialog "Login anlegen" für Schüler/innen und Lehrkräfte (nur Admin).
    Einbinden mit: @include('partials.account-dialog', ['role' => 'student'])  bzw. 'teacher'

    Schickt an die Route "users.store":
    name, email, password, password_confirmation, role und student_id bzw. teacher_id
--}}
@if (auth()->user()->isAdmin() && Route::has('users.store'))
    <x-modal id="account-dialog" :title="$role === 'teacher' ? 'Login für Lehrkraft anlegen' : 'Login für Schüler/in anlegen'" :action="route('users.store')" submit="Login anlegen">
        <input type="hidden" name="role" value="{{ $role }}">
        <input type="hidden" name="{{ $role }}_id" id="account-person-id" value="{{ old($role.'_id') }}">

        <label class="field field-wide">
            <span>Name</span>
            <input name="name" id="account-name" value="{{ old('name') }}" required>
        </label>
        <label class="field field-wide">
            <span>E-Mail (Benutzername)</span>
            <input type="email" name="email" id="account-email" value="{{ old('email') }}" autocomplete="off" required>
        </label>
        <label class="field">
            <span>Startpasswort</span>
            <input type="password" name="password" minlength="8" autocomplete="new-password" required>
        </label>
        <label class="field">
            <span>Passwort wiederholen</span>
            <input type="password" name="password_confirmation" minlength="8" autocomplete="new-password" required>
        </label>
    </x-modal>

    @push('scripts')
        <script>
            function openAccountDialog(id, name, email) {
                document.getElementById('account-person-id').value = id;
                document.getElementById('account-name').value = name;
                document.getElementById('account-email').value = email || '';
                document.getElementById('account-dialog').showModal();
            }
        </script>
    @endpush
@endif
