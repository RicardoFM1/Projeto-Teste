import { useState } from "react";
import {
  Offcanvas,
  Nav,
  Button,
  Container,
  Stack,
  Card,
} from "react-bootstrap";
import { NavLink } from "react-router-dom";
import "../../App.css";
import style from "./sidebar.module.css";
import { MdDashboard } from "react-icons/md";
import { FaUser } from "react-icons/fa";
import { FaUsers } from "react-icons/fa";
import { IoIosCheckmarkCircle } from "react-icons/io";
import { MdTableBar } from "react-icons/md";

function SideBar({ show, telaAtiva, setTelaAtiva }) {
  console.log(telaAtiva);
  console.log(style.botaoAtivo);
  return (
    <>
      <Offcanvas
        className={style.sidebar}
        scroll={true}
        backdrop={false}
        show={show}
      >
        <Offcanvas.Header>
          <Offcanvas.Title className={style["font-casamento"]}>
            Navegação
          </Offcanvas.Title>
        </Offcanvas.Header>
        <Offcanvas.Body className="d-flex flex-column">
          <div className="mb-auto">
            <p className={style["font-casamento"]}>ADMIN</p>

            <Stack gap={2} style={{ maxWidth: 450 }}>
              <Button
                variant="none"
                onClick={() => setTelaAtiva("dashboard")}
                className={`btn ${telaAtiva === "dashboard" ? style["botaoAtivo"] : ""}`}
              >
                <MdDashboard size={20} /> Dashboard
              </Button>

              <Button
                onClick={() => setTelaAtiva("usuarios")}
                className={`btn ${telaAtiva === "usuarios" ? style.botaoAtivo : ""}`}
              >
                <FaUser size={20} /> Usuários
              </Button>
            </Stack>

            <p className={style["font-casamento"]}>ADMIN E CEREMONIALISTAS</p>

            <Stack gap={2} style={{ maxWidth: 450 }}>
              <Button
                onClick={() => setTelaAtiva("convidados_acompanhantes")}
                className={`btn ${telaAtiva === "convidados_acompanhantes" ? style.botaoAtivo : ""}`}
              >
                <FaUsers size={20} /> Convidados e acompanhantes
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
            </Stack>
          </div>

          <div>
            <Card>
              <Card.Header>
                <div className="d-flex flex-row align-items-center">
                  <Stack>
                    <Card.Title>Username</Card.Title>
                    <Card.Subtitle>Admin</Card.Subtitle>
                  </Stack>
                  <Button size="sm" className={`btn ${style.btnSair}`}>
                    Sair
                  </Button>
                </div>
              </Card.Header>
            </Card>
          </div>
        </Offcanvas.Body>
      </Offcanvas>
    </>
  );
}

export default SideBar;
