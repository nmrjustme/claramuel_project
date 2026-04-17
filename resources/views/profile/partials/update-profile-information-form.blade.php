<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>
    
    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="firstname" :value="__('Firstname')" />
            <x-text-input id="firstname" name="firstname" type="text" class="mt-1 block w-full" :value="old('firstname', $user->firstname)" required autofocus autocomplete="firstname" />
            <x-input-error class="mt-2" :messages="$errors->get('firstname')" />
        </div>
        <div>
            <x-input-label for="lastname" :value="__('Lastname')" />
            <x-text-input id="lastname" name="lastname" type="text" class="mt-1 block w-full" :value="old('lastname', $user->lastname)" required autofocus autocomplete="lastname" />
            <x-input-error class="mt-2" :messages="$errors->get('lastname')" />
        </div>
        <div class="mb-4">
            <x-input-label for="phone" :value="__('Phone Number')" />
            <x-text-input 
                type="text" 
                name="phone" 
                id="phone"
                :value="old('phone', $user->phone)"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                maxlength="13" {{-- max length to fit "9999 999 999" --}}
                oninput="formatPhone(this)"
                placeholder="9999 999 999"
            />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="dark:text-blue-300 text-sm text-gray-600 hover:underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="mt-auto bg-blue-500 hover:bg-blue-600 text-white font-semibold font-semibold py-2 px-6 rounded w-fit self-end">{{ __('Save') }}</button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-800"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
<script>
function formatPhone(input) {
    let value = input.value.replace(/\D/g, '').substring(0, 11); // only digits, max 11 numbers
    let formatted = '';
    
    if (value.length > 4) {
        formatted = value.substring(0, 4) + ' ' + value.substring(4, 7);
    } else {
        formatted = value;
    }

    if (value.length > 7) {
        formatted = value.substring(0, 4) + ' ' + value.substring(4, 7) + ' ' + value.substring(7, 10);
    }

    input.value = formatted.trim();
}
</script>
