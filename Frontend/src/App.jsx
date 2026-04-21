import Header from "./components/Header/header";
import { Container } from "react-bootstrap";
import SideBar from "./components/Sidebar/sidebar";
import { BrowserRouter, Route, Routes } from "react-router-dom";
import "./App.css";
import Dashboard from "./pages/Dashboard/dashboard";
import { useState } from "react";

function App() {
  const [telaAtiva, setTelaAtiva] = useState('dashboard');

  return (
    
    <div>
      <Header telaAtiva={telaAtiva} setTelaAtiva={setTelaAtiva}/>

    <main>
      {/* checar admin */}
      {telaAtiva === 'dashboard' && <Dashboard/>}
    </main>
    </div>
    

  );
}

export default App;
