<?php

namespace App\Http\Controllers\v1\userAdmin;

use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Models\OfficeLocation;
use App\Models\SupportCategory;
use App\Models\SupportHelpline;
use App\Models\SupportQuery;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    use HttpResponse;

    public function supportTicketResolved(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:support_tickets,id'
            ]);
            $ticket = SupportTicket::find($validated['id']);
            $ticket->status = 'closed';

            if ($ticket->update()) {
                return $this->success(
                    message: 'Support ticket resolved successfully'
                );
            } else {
                return $this->error(
                    message: 'Failed to resolve support ticket'
                );
            }

        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getSupportTickets(Request $request)
    {
        try {
            $search = $request->query('search');

            $tickets = SupportTicket::where('ticket_id', 'like', "%$search%")
                ->orderBy('status', 'asc')
                ->orderBy('created_at', 'desc')
                ->with('category', 'user')
                ->paginate(15);

            return $this->success(
                data: $tickets
            );
        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function submitContactForm(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|string',
                'category' => 'required|integer|exists:support_categories,id',
                'message' => 'required|string',
            ]);

            //checking if user already sent 5 request which are pending
            $pendingTickets = SupportTicket::where('user_id', auth()->id())
                ->where('status', 'pending')
                ->count();
            if ($pendingTickets >= 5) {
                return $this->error(
                    message: 'You have reached the maximum number of pending support tickets'
                );
            }

            $ticket = new SupportTicket();
            $ticket->user_id = auth()->id();
            $ticket->name = $validated['name'];
            $ticket->email = $validated['email'];
            $ticket->phone = $validated['phone'];
            $ticket->category_id = $validated['category'];
            $ticket->ticket_id = 'T' . time();
            $ticket->message = $validated['message'];

            if ($ticket->save()) {
                return $this->success(
                    message: 'Support Ticket submitted successfully'
                );
            } else {
                return $this->error(
                    message: 'Failed to submit support ticket'
                );
            }


        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getSupportCategoryQuestions(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_slug' => 'required|string|exists:support_categories,slug'
            ]);

            $category = SupportCategory::where('slug', $validated['category_slug'])
                ->where('is_active', 1)
                ->first();

            $queries = SupportQuery::where('category_id', $category->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->success(
                data: $queries
            );
        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function updateSupportQuery(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:support_queries,id',
                'category_id' => 'required|integer|exists:support_categories,id',
                'question' => 'required|string',
                'answer' => 'required',
            ]);

            $query = SupportQuery::find($validated['id']);
            $query->category_id = $validated['category_id'];
            $query->question = $validated['question'];
            $query->answer = $validated['answer'];

            if ($query->update()) {
                return $this->success(
                    message: 'Support query updated successfully'
                );
            } else {
                return $this->error(
                    message: 'Failed to update support query'
                );
            }

        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function deleteSupportQuery(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:support_queries,id'
            ]);

            $query = SupportQuery::find($validated['id']);

            if ($query->delete()) {
                return $this->success(
                    message: 'Support query deleted successfully'
                );
            } else {
                return $this->error(
                    message: 'Failed to delete support query'
                );
            }

        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getSupportQueries(Request $request)
    {
        try {
            $search = $request->query('search');
            $queries = SupportQuery::where('question', 'like', "%$search%")
                ->orderBy('created_at', 'desc')
                ->with('category')
                ->paginate(15);

            return $this->success(
                data: $queries
            );
        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function addSupportQuery(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required|integer|exists:support_categories,id',
                'question' => 'required|string',
                'answer' => 'required',
            ]);

            $query = new SupportQuery();
            $query->category_id = $validated['category_id'];
            $query->question = $validated['question'];
            $query->answer = $validated['answer'];

            if ($query->save()) {
                return $this->success(
                    message: 'Support query added successfully'
                );
            } else {
                return $this->error(
                    message: 'Failed to add support query'
                );
            }

        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function editSupportCategory(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:support_categories,id',
                'name' => 'required|string',
            ]);

            $category = SupportCategory::find($validated['id']);
            $category->name = $validated['name'];
            if ($category->update()) {
                return $this->success(
                    message: 'Support category updated successfully'
                );
            } else {
                return $this->error(
                    message: 'Failed to update support category'
                );
            }
        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getAllSupportCategory()
    {
        try {
            $categories = SupportCategory::where('is_active', 1)->get();
            return $this->success(
                data: $categories
            );
        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function deleteSupportCategory(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:support_categories,id'
            ]);

            $category = SupportCategory::find($validated['id']);
            if ($category->delete()) {
                return $this->success(
                    message: 'Support category deleted successfully'
                );
            } else {
                return $this->error(
                    message: 'Failed to delete support category'
                );
            }
        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getSupportCategory()
    {
        try {
            $categories = SupportCategory::paginate(15);

            foreach ($categories as $category) {
                $category->queries_count = $category->queries()->count();
            }

            return $this->success(
                data: $categories
            );
        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function addSupportCategory(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
            ]);

            $category = new SupportCategory();
            $category->name = $validated['name'];
            if ($category->save()) {
                return $this->success(
                    message: 'Support category added successfully'
                );
            } else {
                return $this->error(
                    message: 'Failed to add support category'
                );
            }

        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getAllOfficeLocationForUsers()
    {
        try {
            $locations = OfficeLocation::all();
            return $this->success(
                data: $locations
            );
        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getAllSupportHelplines()
    {
        try {
            $locations = SupportHelpline::all();
            return $this->success(
                data: $locations
            );
        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function deleteHelpline(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:support_helplines,id'
            ]);
            $helpline = SupportHelpline::find($validated['id']);

            if ($helpline->delete()) {
                return $this->success(
                    message: 'Helpline deleted successfully'
                );
            } else {
                return $this->error(
                    message: 'Failed to delete helpline'
                );
            }
        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }


    public function deleteOfficeLocation(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:office_locations,id'
            ]);

            $location = OfficeLocation::find($validated['id']);
            if ($location->delete()) {
                return $this->success(
                    message: 'Office location deleted successfully'
                );
            } else {
                return $this->error(
                    message: 'Failed to delete office location'
                );
            }

        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function addSupportHelpline(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string',
                'email' => 'nullable|string',
                'phone' => 'nullable|string'
            ]);

            $location = new SupportHelpline();
            $location->title = $validated['title'];
            $location->mail = $validated['email'] ?? null;
            $location->phone = $validated['phone'] ?? null;

            if ($location->save()) {
                return $this->success(
                    message: 'Office location added successfully'
                );
            } else {
                return $this->error(
                    message: 'Failed to add office location'
                );
            }

        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function editSupportHelpline(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:support_helplines,id',
                'title' => 'required|string',
                'email' => 'nullable|string',
                'phone' => 'nullable|string'
            ]);

            $location = SupportHelpline::find($validated['id']);
            $location->title = $validated['title'];
            $location->mail = $validated['email'] ?? null;
            $location->phone = $validated['phone'] ?? null;

            if ($location->update()) {
                return $this->success(
                    message: 'Office location updated successfully'
                );
            } else {
                return $this->error(
                    message: 'Failed to update office location'
                );
            }

        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function addOfficeLocation(Request $request)
    {
        try {
            $validated = $request->validate([
                'location' => 'required|string',
                'address' => 'required|string',
                'phone' => 'required|string',
                'email' => 'required|email',
                'whatsapp' => 'nullable|string',
                'is_main' => 'nullable',
                'iframe' => 'nullable|string',
            ]);

            $location = new OfficeLocation();
            $location->location = $validated['location'];
            $location->address = $validated['address'];
            $location->phone = $validated['phone'];
            $location->email = $validated['email'];
            $location->whatsapp = $validated['whatsapp'] ?? null;
            $location->is_main = $validated['is_main'] == 1 ? 1 : 0;
            $location->iframe = $validated['iframe'] ? $validated['iframe'] ?? null : null;

            if ($location->save()) {
                // If the location is set as main, update all other locations to not main
                if ($location->is_main) {
                    OfficeLocation::where('id', '!=', $location->id)
                        ->update(['is_main' => 0, 'iframe' => null]);
                }
                return $this->success(
                    message: 'Office location added successfully'
                );
            } else {
                return $this->error(
                    message: 'Failed to add office location'
                );
            }

        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function updateOfficeLocation(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:office_locations,id',
                'location' => 'required|string',
                'address' => 'required|string',
                'phone' => 'required|string',
                'email' => 'required|email',
                'whatsapp' => 'nullable|string',
                'is_main' => 'nullable',
                'iframe' => 'nullable|string',
            ]);

            $location = OfficeLocation::find($validated['id']);
            $location->location = $validated['location'];
            $location->address = $validated['address'];
            $location->phone = $validated['phone'];
            $location->email = $validated['email'];
            $location->whatsapp = $validated['whatsapp'] ?? null;
            $location->is_main = $validated['is_main'] == 1 ? 1 : 0;
            $location->iframe = $validated['iframe'] ? $validated['iframe'] ?? null : null;

            if ($location->update()) {
                // If the location is set as main, update all other locations to not main
                if ($location->is_main) {
                    OfficeLocation::where('id', '!=', $location->id)
                        ->update(['is_main' => 0, 'iframe' => null]);
                }
                return $this->success(
                    message: 'Office location updated successfully'
                );
            } else {
                return $this->error(
                    message: 'Failed to update office location'
                );
            }

        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getSupportHelplines()
    {
        try {
            $search = request()->query('search');
            $locations = SupportHelpline::where('title', 'like', "%$search%")
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return $this->success(
                data: $locations
            );
        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getOfficeLocations()
    {
        try {
            $search = request()->query('search');
            $locations = OfficeLocation::where('location', 'like', "%$search%")
                ->orderBy('is_main', 'desc')
                ->paginate(15);

            return $this->success(
                data: $locations
            );
        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }
}
