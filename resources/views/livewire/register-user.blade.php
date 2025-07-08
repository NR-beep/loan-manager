
<div>
    <form wire:submit.prevent="register">
        <input wire:model="name" type="text" placeholder="Name" />
        @error('name') <span>{{ $message }}</span> @enderror

        <input wire:model="email" type="email" placeholder="Email" />
        @error('email') <span>{{ $message }}</span> @enderror

        <input wire:model="password" type="password" placeholder="Password" />
        @error('password') <span>{{ $message }}</span> @enderror

        <button type="submit">Register</button>
    </form>
</div>
