import { Container } from "react-bootstrap";
import Header from "../../components/Header/header";
import SideBar from "../../components/Sidebar/sidebar";
import Dashboard from "../../components/Dashboard/dashboard";
import Usuarios from "../../components/Usuarios/usuarios";

function Home({ telaAtiva, setTelaAtiva, show, setShow }) {
  return (
    <>
      <Header
        telaAtiva={telaAtiva}
        setTelaAtiva={setTelaAtiva}
        show={show}
        setShow={setShow}
      />
      <SideBar telaAtiva={telaAtiva} setTelaAtiva={setTelaAtiva} show={show} />

      <main style={{ flexGrow: 1, marginLeft: show ? '450px' : '0', padding: '20px', transition: 'all, 0.5s' }}>
        {/* checar admin */}
        {telaAtiva === "dashboard" && <Dashboard />}
        {telaAtiva === "usuarios" && <Usuarios/>}
      </main>
    </>
  );
}

export default Home;
