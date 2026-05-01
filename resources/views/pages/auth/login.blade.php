<x-layouts::auth.split :title="__('Log in')">
    <section class="auth-card">
        <header class="auth-card-header">
            <p class="auth-eyebrow">
                {{ __('Welcome back') }}
            </p>
            <div>
                <flux:heading size="xl">{{ __('Log in to Traffic Reports') }}</flux:heading>
                <flux:subheading>{{ __('Continue tracking traffic offence reports, evidence, and review decisions.') }}</flux:subheading>
            </div>
        </header>

        <!-- Session Status -->
        <div class="auth-status">
            <x-auth-session-status class="text-center" :status="session('status')" />
        </div>

        <div class="auth-card-body">
            <form method="POST" action="{{ route('login.store') }}" class="auth-form">
                @csrf

                <!-- Email Address -->
                <flux:input
                    name="email"
                    :label="__('Email address')"
                    :value="old('email')"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="email@example.com"
                />

                <!-- Password -->
                <div class="relative">
                    <flux:input
                        name="password"
                        :label="__('Password')"
                        type="password"
                        required
                        autocomplete="current-password"
                        :placeholder="__('Password')"
                        viewable
                    />

                    @if (Route::has('password.request'))
                        <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                            {{ __('Forgot password?') }}
                        </flux:link>
                    @endif
                </div>

                <div class="auth-option-row">
                    <flux:checkbox name="remember" :label="__('Keep me signed in')" :checked="old('remember')" />
                    <span>{{ __('Secure access') }}</span>
                </div>

                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Log in') }}
                </flux:button>
            </form>

            @if (Route::has('register'))
                <div class="auth-switch">
                    <span>{{ __('New to the reporting portal?') }}</span>
                    <flux:link :href="route('register')" wire:navigate>{{ __('Create an account') }}</flux:link>
                </div>
            @endif
        </div>
    </section>
</x-layouts::auth.split>
