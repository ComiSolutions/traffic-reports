<x-layouts::auth.split :title="__('Log in')">
    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-white/10 dark:bg-neutral-900">
        <div class="border-b border-zinc-100 bg-zinc-50 px-6 py-5 dark:border-white/10 dark:bg-white/5 sm:px-8">
            <p class="text-sm font-semibold uppercase tracking-normal text-emerald-700 dark:text-emerald-300">
                {{ __('Welcome back') }}
            </p>
            <div class="mt-2">
                <flux:heading size="xl">{{ __('Log in to Traffic Reports') }}</flux:heading>
                <flux:subheading>{{ __('Continue tracking traffic offence reports, evidence, and review decisions.') }}</flux:subheading>
            </div>
        </div>

        <!-- Session Status -->
        <div class="px-6 pt-6 sm:px-8">
            <x-auth-session-status class="text-center" :status="session('status')" />
        </div>

        <div class="px-6 py-6 sm:px-8">
            <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
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

                <div class="flex items-center justify-between gap-4 rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                    <flux:checkbox name="remember" :label="__('Keep me signed in')" :checked="old('remember')" />
                    <span class="hidden text-xs text-zinc-500 dark:text-zinc-400 sm:block">{{ __('Secure access') }}</span>
                </div>

                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Log in') }}
                </flux:button>
            </form>

            @if (Route::has('register'))
                <div class="mt-6 rounded-lg bg-emerald-50 px-4 py-3 text-center text-sm text-emerald-900 dark:bg-emerald-400/10 dark:text-emerald-100">
                    <span>{{ __('New to the reporting portal?') }}</span>
                    <flux:link :href="route('register')" wire:navigate>{{ __('Create an account') }}</flux:link>
                </div>
            @endif
        </div>
    </div>
</x-layouts::auth.split>
