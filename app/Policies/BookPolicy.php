<?php 

// namespace App\Policies;

// use App\Models\Book;
// use App\Models\User;
// use Illuminate\Auth\Access\Response;

// class BookPolicy
// {
 
//     public function viewAny(User $user): bool
//     {
//         return false;
//     }


//     public function view(User $user, Book $book): bool
//     {
//         return false;
//     }


//     public function create(User $user): bool
//     {
//         return false;
//     }

 
//     public function update(User $user, Book $book): bool
//     {
//         return false;
//     }

  
//     public function delete(User $user, Book $book): bool
//     {
//         return false;
//     }

   
//     public function restore(User $user, Book $book): bool
//     {
//         return false;
//     }

   
//     public function forceDelete(User $user, Book $book): bool
//     {
//         return false;
//     }
// }

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    /**
     * Ai cũng xem được danh sách sách.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Ai cũng xem được chi tiết sách.
     */
    public function view(?User $user, Book $book): bool
    {
        return true;
    }

    /**
     * Chỉ Admin được thêm sách.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Chỉ Admin được sửa sách.
     */
    public function update(User $user, Book $book): bool
    {
        return $user->isAdmin();
    }

    /**
     * Chỉ Admin được xóa (xóa tạm) sách.
     */
    public function delete(User $user, Book $book): bool
    {
        return $user->isAdmin();
    }

    /**
     * Chỉ Admin được khôi phục sách từ thùng rác.
     */
    public function restore(User $user, Book $book): bool
    {
        return $user->isAdmin();
    }

    /**
     * Chỉ Admin được xóa vĩnh viễn.
     */
    public function forceDelete(User $user, Book $book): bool
    {
        return $user->isAdmin();
    }
}