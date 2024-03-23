<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Notifications\UserDeletedNotification;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\{On, Rule};
use Livewire\Component;
use Mary\Traits\Toast;

class Delete extends Component
{
    use Toast;

    public ?User $user = null;

    #[Rule(['required', 'confirmed'])]
    public string $confirmation = 'I WANT TO DELETE';

    public ?string $confirmation_confirmation = null;

    public bool $modal = false;

    public function render(): View
    {
        return view('livewire.admin.users.delete');
    }

    #[On('user::deletion')]
    public function openConfirmationFor(int $userID): void
    {
        $this->user  = User::select('id', 'name')->find($userID);
        $this->modal = true;
    }

    public function destroy(): void
    {

        $this->validate();

        if ($this->isSameUser()) {
            $this->addError('confirmation', "You can't delete yourself.");

            return;
        }

        $this->user->delete();

        $this->user->deleted_by = auth()->user()->id;
        $this->user->save();

        $this->success('User deleted successfully!');

        $this->user->notify(new UserDeletedNotification());

        $this->dispatch('user::deleted');

        $this->reset('modal');

    }

    private function isSameUser(): bool
    {
        return $this->user->is(auth()->user());
    }
}
