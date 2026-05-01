<x-layouts::auth.split :title="__('Register')">
    <section class="auth-card">
        <header class="auth-card-header">
            <p class="auth-eyebrow">
                {{ __('Join the portal') }}
            </p>
            <div>
                <flux:heading size="xl">{{ __('Create your Traffic Reports account') }}</flux:heading>
                <flux:subheading>{{ __('Set up access to submit offences, upload evidence, and follow review progress.') }}</flux:subheading>
            </div>
        </header>

        <!-- Session Status -->
        <div class="auth-status">
            <x-auth-session-status class="text-center" :status="session('status')" />
        </div>

        <div class="auth-card-body">
            <form method="POST" action="{{ route('register.store') }}" class="auth-form">
                @csrf
                <!-- Name -->
                <flux:input
                    name="name"
                    :label="__('Name')"
                    :value="old('name')"
                    type="text"
                    required
                    autofocus
                    autocomplete="name"
                    :placeholder="__('Full name')"
                />

                <!-- Email Address -->
                <flux:input
                    name="email"
                    :label="__('Email address')"
                    :value="old('email')"
                    type="email"
                    required
                    autocomplete="email"
                    placeholder="email@example.com"
                />

                <div class="auth-password-grid">
                    <!-- Password -->
                    <flux:input
                        name="password"
                        :label="__('Password')"
                        type="password"
                        required
                        autocomplete="new-password"
                        :placeholder="__('Password')"
                        viewable
                    />

                    <!-- Confirm Password -->
                    <flux:input
                        name="password_confirmation"
                        :label="__('Confirm password')"
                        type="password"
                        required
                        autocomplete="new-password"
                        :placeholder="__('Confirm password')"
                        viewable
                    />
                </div>

                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
            </form>

            <div class="auth-mini-grid">
                <div>{{ __('Evidence') }}</div>
                <div>{{ __('Location') }}</div>
                <div>{{ __('Review') }}</div>
            </div>

            <div class="auth-switch">
                <span>{{ __('Already have an account?') }}</span>
                <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
            </div>
        </div>
    </section>
</x-layouts::auth.split>
