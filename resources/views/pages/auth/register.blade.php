<x-layouts::auth.split :title="__('Register')">
    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-white/10 dark:bg-neutral-900">
        <div class="border-b border-zinc-100 bg-zinc-50 px-6 py-5 dark:border-white/10 dark:bg-white/5 sm:px-8">
            <p class="text-sm font-semibold uppercase tracking-normal text-emerald-700 dark:text-emerald-300">
                {{ __('Join the portal') }}
            </p>
            <div class="mt-2">
                <flux:heading size="xl">{{ __('Create your Traffic Reports account') }}</flux:heading>
                <flux:subheading>{{ __('Set up access to submit offences, upload evidence, and follow review progress.') }}</flux:subheading>
            </div>
        </div>

        <!-- Session Status -->
        <div class="px-6 pt-6 sm:px-8">
            <x-auth-session-status class="text-center" :status="session('status')" />
        </div>

        <div class="px-6 py-6 sm:px-8">
            <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5">
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

                <div class="grid gap-5 sm:grid-cols-2">
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

            <div class="mt-6 grid grid-cols-3 gap-2 text-center text-xs text-zinc-500 dark:text-zinc-400">
                <div class="rounded-lg border border-zinc-200 px-2 py-3 dark:border-white/10">{{ __('Evidence') }}</div>
                <div class="rounded-lg border border-zinc-200 px-2 py-3 dark:border-white/10">{{ __('Location') }}</div>
                <div class="rounded-lg border border-zinc-200 px-2 py-3 dark:border-white/10">{{ __('Review') }}</div>
            </div>

            <div class="mt-6 rounded-lg bg-emerald-50 px-4 py-3 text-center text-sm text-emerald-900 dark:bg-emerald-400/10 dark:text-emerald-100">
                <span>{{ __('Already have an account?') }}</span>
                <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
            </div>
        </div>
    </div>
</x-layouts::auth.split>
