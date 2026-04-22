import { Container } from "react-bootstrap";
import { BrowserRouter, Route, Routes } from "react-router-dom";
import "./App.css";

import { useState } from "react";
import Home from "./pages/Home/home";

function App() {
  const [telaAtiva, setTelaAtiva] = useState("dashboard");
  const [show, setShow] = useState(true);

  return (
    <Home
      telaAtiva={telaAtiva}
      setTelaAtiva={setTelaAtiva}
      show={show}
      setShow={setShow}
    />
  );
}

export default App;
