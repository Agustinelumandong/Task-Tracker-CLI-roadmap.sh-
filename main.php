<?php

// Task Array
$tasks = [];

// Main script
$tasks = json_decode(file_get_contents('tasks.json'), true);
$command = $argv[1] ?? null;
$description = $argv[2] ?? null;
$listStatus = isset($argv[2]) && in_array($argv[2], ['todo', 'in-progress', 'done']) ? $argv[2] : null;
$taskId = $argv[2] ?? null;
$updateDescription = $argv[3] ?? null;


// This code check the command and execute the command
if ($command === 'add') {
  if ($description === null) {
    echo "Error: Description is required for adding a task.\n";
    exit(1);
  }
  addTask($tasks, $description);
} elseif ($command === 'update') {
  updateTask($tasks, $taskId, $updateDescription);
} elseif ($command === 'delete') {
  deleteTask($tasks, $taskId);
} elseif ($command === 'mark-in-progress' || $command === 'mark-done') {
  if ($taskId === null) {
    echo "Error: task Id or number is required for marking in progress.\n";
    exit(1);
  }
  if ($command === 'mark-in-progress') {
    markInProgress($tasks, $taskId);
  } else if ($command === 'mark-done') {
    markDone($tasks, $taskId);
  }
} elseif ($command === 'list') {
  if ($listStatus) {
    listTasksByStatus($tasks, $listStatus);
  } else {
    listAllTasks($tasks);
  }
} else {
  echo "Invalid command\n";
}
// End of check the command and execute the command

// This code save or update the json file
function saveTasksToFile($tasks)
{
  file_put_contents('tasks.json', json_encode($tasks, JSON_PRETTY_PRINT));
}
// End of save or update the json file

// This code add task to the json file
function addTask(&$tasks, $description)
{
  $id = $tasks ? $tasks[count($tasks) - 1]['id'] + 1 : 1;
  $createdAt = date('Y-m-d H:i:s');
  $updatedAt = $createdAt;
  $tasks[] = [
    'id' => $id,
    'description' => $description,
    'status' => 'todo',
    'createdAt' => $createdAt,
    'updatedAt' => $updatedAt
  ];
  echo "Task added successfully (ID: $id)\n";
  saveTasksToFile($tasks);
}
// End of add task to the json file

// This code update the task in the json file
function updateTask(&$tasks, $taskId, $updateDescription)
{
  $update = $updateDescription;
  $updateAt = date('Y-m-d H:i:s');
  foreach ($tasks as &$task) {
    if ($task['id'] == $taskId) {
      $task['description'] = $update;
      $task['updatedAt'] = $updateAt;
      saveTasksToFile($tasks);
      echo "Task " . $task['description'] . " " . $task['id'] . " ID update successfully";
    } else {
      echo "Error";
      return;
    }
  }
}
// End of update the task in the json file

// This code delete the task in the json file
function deleteTask(&$tasks, $taskId)
{
  $taskIndex = null;
  foreach ($tasks as $index => $task) {
    if ($task['id'] == $taskId) {
      $taskIndex = $index;

      // This "array_splice" Remove task from json file tasks array
      array_splice($tasks, $taskIndex, 1);
      // end of array_splice

      echo "Task deleted successfully\n";
      saveTasksToFile($tasks);
      break;
    } else if ($taskIndex === null) {
      echo "Task not found\n";
      return;
    }
  }
}
// End of delete the task in the json file

// This code mark the task in progress in the json file
function markInProgress(&$tasks, $taskId)
{
  $status = 'in-progress';
  $updatedAt = date('Y-m-d H:i:s');
  foreach ($tasks as &$task) {
    if ($task['id'] == $taskId) {
      if ($task['status'] == 'in-progress') {
        echo "Task is already in-progress\n";
        return;
      } else if ($task['status'] == 'done') {
        echo "Task is already done\n";
        return;
      } else {
        $task['status'] = $status;
        $task['updatedAt'] = $updatedAt;
        saveTasksToFile($tasks);
      }
    }
  }
  echo "Task marked as in-progress successfully\n";
}
// End of mark the task in progress in the json file

// This code mark the task done in the json file
function markDone(&$tasks,  $taskId)
{
  $status = 'done';
  $updatedAt = date('Y-m-d H:i:s');
  foreach ($tasks as &$task) {
    if ($task['id'] == $taskId) {
      if ($task['status'] == 'done') {
        echo "Task is already done\n";
        return;
      } else {
        $task['status'] = $status;
        $task['updatedAt'] = $updatedAt;
        saveTasksToFile($tasks);
        echo "Task marked as done successfully\n";
      }
    }
  }
}
// End of mark the task done in the json file

// This code list all the tasks in the json file
function listAllTasks(&$tasks)
{
  echo "Tasks:\n";
  foreach ($tasks as $tasks['id'] => $task) {
    echo  $task['id'] . ". ";

    echo $task['description'];

    if ($task['status'] == 'todo') {
      echo "[todo] " . "\n";;
    } else if ($task['status'] == 'in-progress') {
      echo "[in-progress] " . "\n";
    } else if ($task['status'] == 'done') {
      echo "[done] " . "\n";;
    }
  }
}
// End of list all the tasks in the json file

// This code list the tasks by status in the json file
function listTasksByStatus(&$tasks, $status)
{
  echo ucfirst($status) . " Tasks List:\n";
  foreach ($tasks as $task) {
    if ($task['status'] == $status) {
      echo  $task['id'] . ". ";
      echo $task['description'] . " [" . $task['status'] . "]" . "\n";
    }
  }
}
// End of list the tasks by status in the json file
