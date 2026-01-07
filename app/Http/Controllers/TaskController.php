<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Calendar;

class TaskController extends Controller
{
public function store(Request $request)
{
    $validated = $request->validate([
        'name' =>'required|string|max:255',
        'user_id' => 'required|exists:users,id',
        'calendar_id' => 'required|exists:calendars,id',
        'notes' => 'nullable|string',
        'done' => 'boolean',
        'due_date' => 'nullable|date',
        'sub_tasks' => 'nullable|array',
        'attachments' => 'nullable|array',
    ]);

    $task = new Task($validated);
    $task->save();

    return to_route('calendar.show', ['user_id' =>$request->user()->id, 'calendar' => Calendar::findOrFail($validated['calendar_id'])])->with('success', 'Task created successfully.');
}

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        
        $validated = $request->validate([
            'name' =>'sometimes|required|string|max:255',
            'user_id' => 'sometimes|required|exists:users,id',
            'calendar_id' => 'sometimes|required|exists:calendars,id',
            'notes' => 'sometimes|nullable|string',
            'done' => 'sometimes|boolean',
            'due_date' => 'sometimes|nullable|date',
            'sub_tasks' => 'sometimes|nullable|array',
            'attachments' => 'sometimes|nullable|array',
        ]);

        $task->fill($validated);

        if ($task->isDirty()) {
            $task->save();
            $calendar = Calendar::findOrFail($validated['calendar_id'] ?? $task->calendar_id);
            return to_route('calendar.show', ['user_id' => $request->user()->id, 'calendar' => $calendar])->with('success', 'Task updated successfully.');
        }

        return back()->with('info', 'No changes made to the task.');
    }
}
