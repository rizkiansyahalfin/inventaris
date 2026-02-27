<form method="POST" action="{{ route('profile.destroy') }}" class="space-y-6">
    @csrf
    @method('delete')
    <div>
        <button type="button" @click="open = true"
            class="py-2 px-4 bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-colors">{{ __('Delete Account') }}</button>
    </div>
    <div x-data="{ open: false }" x-show="open"
        class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">
                {{ __('Are you sure you want to delete your account?') }}</h2>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                <div class="mb-4">
                    <label for="password"
                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Password') }}</label>
                    <input id="password" name="password" type="password"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="{{ __('Password') }}">
                    @if($errors->userDeletion && $errors->userDeletion->has('password'))
                        <div class="mt-2 text-sm text-red-600">{{ $errors->userDeletion->first('password') }}</div>
                    @endif
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="open = false"
                        class="py-2 px-4 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-600 font-semibold rounded-xl transition-colors">{{ __('Cancel') }}</button>
                    <button type="submit"
                        class="py-2 px-4 bg-red-600 dark:bg-red-500 hover:bg-red-700 dark:hover:bg-red-600 text-white font-semibold rounded-xl transition-colors">{{ __('Delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</form>