import { useEffect, useState } from "react";
import {
  Offcanvas,
  Nav,
  Button,
  Container,
  Stack,
  Card,
} from "react-bootstrap";
import { NavLink, useNavigate } from "react-router-dom";
import "../../App.css";
import style from "./sidebar.module.css";
import { MdDashboard } from "react-icons/md";
import { FaUser } from "react-icons/fa";
import { FaUsers } from "react-icons/fa";
import { IoIosCheckmarkCircle } from "react-icons/io";
import { MdTableBar } from "react-icons/md";
import Api from "../../API/api";

function SideBar({ telaAtiva, setTelaAtiva, show, setShow }) {
  const [retrieve, setRetrieve] = useState(null);
    const navigate = useNavigate()
  const buscarRetrieve = async () => {
    try {
      const res = await Api.get("/retrieve");

      if (res.status === 200) {
        setRetrieve(res.data.dados);
      }
    } catch (err) {
      console.log(err);
    }
  };

  useEffect(() => {
    buscarRetrieve()
  }, [])   

  const logout = () => {
    localStorage.removeItem('token')
    navigate('/login')
  }

  return (
    <>
      <Offcanvas
        className={style.sidebar}
        scroll={true}
        backdrop={false}
        show={show}
      >
        <Offcanvas.Body className="d-flex flex-column mt-5">
          <div className="mb-auto">
            {retrieve && retrieve?.cargo_usuario === "admin" ? (
              <>
                <p className={style["font-casamento"]}>ADMIN</p>

                <Stack gap={2} style={{ maxWidth: 450 }}>
                  <Button
                    variant="none"
                    onClick={() => setTelaAtiva("dashboard")}
                    className={`btn ${telaAtiva === "dashboard" ? style["botaoAtivo"] : ""}`}
                  >
                    <MdDashboard size={20} /> Dashboard
                  </Button>
                </Stack>
              </>
            ) : (
              ""
            )}

            <p className={style["font-casamento"]}>ADMIN E CEREMONIALISTAS</p>

            <Stack gap={2} style={{ maxWidth: 450 }}>
              <Button
                onClick={() => setTelaAtiva("convidados")}
                className={`btn ${telaAtiva === "convidados" ? style.botaoAtivo : ""}`}
              >
                <FaUsers size={20} /> Convidados
              </Button>

              <Button
                onClick={() => setTelaAtiva("checkins")}
                className={`btn ${telaAtiva === "checkins" ? style.botaoAtivo : ""}`}
              >
                <IoIosCheckmarkCircle size={20} /> Checkins
              </Button>
              <Button
                onClick={() => setTelaAtiva("mesas")}
                className={`btn ${telaAtiva === "mesas" ? style.botaoAtivo : ""}`}
              >
                <MdTableBar size={20} /> Mesas
              </Button>
              <Button
                onClick={() => setTelaAtiva("acompanhantes")}
                className={`btn ${telaAtiva === "acompanhantes" ? style.botaoAtivo : ""}`}
              >
                <MdTableBar size={20} /> Acompanhantes
              </Button>
            </Stack>
          </div>
           {retrieve && (
          <Card className="mt-auto border-0 shadow rounded-4">
  <Card.Body className="p-3">
    
  
    <div className="d-flex align-items-center mb-3">
      

      <div>
        <div style={{  fontWeight: 600 }}>
          {retrieve.email_usuario}
        </div>
        <hr className="my-1"/>
        <div style={{  color: "#888" }}>
          {retrieve.cargo_usuario}
        </div>
      </div>
    </div>

    
    <Button
      variant="outline-danger"
      size="sm"
      className="w-100 rounded-3"
      onClick={logout}
    >
      Sair
    </Button>
  </Card.Body>
</Card>
        )}

        </Offcanvas.Body>
        
      </Offcanvas>
    </>
  );
}

export default SideBar;
