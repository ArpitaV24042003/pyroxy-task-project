import { useState, useEffect } from "react";
import "./App.css";

const API = "https://pyroxytask.infinityfreeapp.com/";

function App() {
  // ================= USER =================
  const [user, setUser] = useState(() =>
    JSON.parse(localStorage.getItem("user")),
  );

  const [isLogin, setIsLogin] = useState(true);

  // ================= AUTH FIELDS =================
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [phone, setPhone] = useState("");
  const [password, setPassword] = useState("");

  // ================= TASK STATE =================
  const [tasks, setTasks] = useState([]);
  const [title, setTitle] = useState("");
  const [reminder, setReminder] = useState("");
  const [editId, setEditId] = useState(null);

  // ================= LOAD TASKS =================
  useEffect(() => {
    if (!user?.id) return;

    const loadTasks = async () => {
      try {
        const res = await fetch(`${API}read.php?user_id=${user.id}`);
        const data = await res.json();
        setTasks(Array.isArray(data) ? data : []);
      } catch (err) {
        console.error("Fetch error:", err);
      }
    };

    loadTasks();
  }, [user?.id]);

  // ================= AUTH =================
  const handleAuth = async () => {
    const endpoint = isLogin ? "login.php" : "register.php";

    const body = isLogin
      ? { email, password }
      : { name, email, phone, password };

    try {
      const res = await fetch(API + endpoint, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(body),
      });

      const data = await res.json();

      if (data.error) {
        alert(data.error);
        return;
      }

      localStorage.setItem("user", JSON.stringify(data));
      setUser(data);

      setName("");
      setEmail("");
      setPhone("");
      setPassword("");
    } catch (err) {
      console.error("Auth error:", err);
    }
  };

  // ================= REFRESH TASKS =================
  const refreshTasks = async () => {
    if (!user?.id) return;

    try {
      const res = await fetch(`${API}read.php?user_id=${user.id}`);
      const data = await res.json();
      setTasks(Array.isArray(data) ? data : []);
    } catch (err) {
      console.error("Refresh error:", err);
    }
  };

  // ================= ADD / UPDATE =================
  const addOrUpdateTask = async () => {
    if (!title.trim()) return;

    const endpoint = editId ? "update.php" : "create.php";

    const body = editId
      ? { id: editId, title, reminder_time: reminder }
      : { user_id: user.id, title, reminder_time: reminder };

    try {
      const res = await fetch(API + endpoint, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(body),
      });

      const data = await res.json();

      if (data.error) {
        alert(data.error);
        return;
      }

      setTitle("");
      setReminder("");
      setEditId(null);

      refreshTasks(); // 🔥 refresh after add/update
    } catch (err) {
      console.error("Task error:", err);
    }
  };

  // ================= DELETE =================
  const deleteTask = async (id) => {
    try {
      await fetch(API + "delete.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id }),
      });

      refreshTasks(); // 🔥 refresh after delete
    } catch (err) {
      console.error("Delete error:", err);
    }
  };

  // ================= EDIT =================
  const editTask = (task) => {
    setTitle(task.title);
    setReminder(task.reminder_time || "");
    setEditId(task.id);
  };

  // ================= LOGOUT =================
  const logout = () => {
    localStorage.removeItem("user");
    setUser(null);
    setTasks([]);
  };

  // ================= UI =================
  return (
    <div className="container">
      {!user ? (
        <div className="card auth-card">
          <h2>{isLogin ? "Login" : "Create Account"}</h2>

          {!isLogin && (
            <input
              placeholder="Full Name"
              value={name}
              onChange={(e) => setName(e.target.value)}
            />
          )}

          <input
            placeholder="Email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
          />

          {!isLogin && (
            <input
              placeholder="Phone"
              value={phone}
              onChange={(e) => setPhone(e.target.value)}
            />
          )}

          <input
            type="password"
            placeholder="Password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
          />

          <button onClick={handleAuth}>{isLogin ? "Login" : "Register"}</button>

          <p className="switch" onClick={() => setIsLogin(!isLogin)}>
            {isLogin
              ? "Don't have an account? Register"
              : "Already have an account? Login"}
          </p>
        </div>
      ) : (
        <div className="card dashboard">
          <div className="header">
            <h2>Welcome, {user.name}</h2>
            <button className="logout" onClick={logout}>
              Logout
            </button>
          </div>

          <div className="task-form">
            <input
              placeholder="Task title"
              value={title}
              onChange={(e) => setTitle(e.target.value)}
            />

            <input
              type="time"
              value={reminder}
              onChange={(e) => setReminder(e.target.value)}
            />

            <button onClick={addOrUpdateTask}>
              {editId ? "Update Task" : "Add Task"}
            </button>
          </div>

          <ul className="task-list">
            {tasks.length === 0 && <p>No tasks yet.</p>}

            {tasks.map((task) => (
              <li key={task.id}>
                <div className="task-title">
                  <span>{task.title}</span>
                  {task.reminder_time && (
                    <span className="task-time">⏰ {task.reminder_time}</span>
                  )}
                </div>

                <div className="actions">
                  <button onClick={() => editTask(task)}>Edit</button>
                  <button onClick={() => deleteTask(task.id)}>Delete</button>
                </div>
              </li>
            ))}
          </ul>
        </div>
      )}
    </div>
  );
}

export default App;
