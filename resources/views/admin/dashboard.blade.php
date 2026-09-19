{{-- ponytail: placeholder sampai layout admin (Sesi 27) & dashboard (Sesi 28). --}}
<x-guest-layout>
    <p class="text-gray-800">Halo, {{ auth('admin')->user()->nama }}.</p>

    <form method="POST" action="{{ route('admin.logout') }}" class="mt-4">
        @csrf
        <x-primary-button>{{ __('Log Out') }}</x-primary-button>
    </form>
</x-guest-layout>
