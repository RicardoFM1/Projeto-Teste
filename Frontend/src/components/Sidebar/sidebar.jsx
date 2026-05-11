import { Button, Offcanvas, Stack } from "react-bootstrap";
import style from "./sidebar.module.css";
import { MdDashboard } from "react-icons/md";
import { FaUsers } from "react-icons/fa6";
import { FaUserCheck } from "react-icons/fa";
import { FaCheck } from "react-icons/fa";
import { MdOutlineTableRestaurant } from "react-icons/md";

function SideBar({ telaAtiva, setTelaAtiva, show, setShow }) {
  console.log(telaAtiva);
  return (
    <>
      <Offcanvas
        className={style.sidebar}
        scroll={true}
        backdrop={false}
        show={show}
      >
        <Offcanvas.Body className="mt-5">
          <p className={style["font-casamento"]}>Admin</p>
          <Stack gap={2}>
            <Button
              onClick={() => setTelaAtiva("dashboard")}
              className={telaAtiva === "dashboard" ? style.botaoAtivo : ""}
            >
              <span className="d-flex">
                <MdDashboard size={25} /> Dashboard{" "}
              </span>
            </Button>
          </Stack>
          <hr />

          <p className={style["font-casamento"]}>Admin e ceremonialistas</p>
          <Stack gap={2}>
            <Button
              onClick={() => setTelaAtiva("convidados")}
              className={telaAtiva === "convidados" ? style.botaoAtivo : ""}
            >
              <span className="d-flex">
                <FaUsers size={25} /> Convidados
              </span>
            </Button>

            <Button
              onClick={() => setTelaAtiva("acompanhantes")}
              className={telaAtiva === "acompanhantes" ? style.botaoAtivo : ""}
            >
              <span className="d-flex">
                <FaUserCheck size={25} /> Acompanhantes
              </span>
            </Button>

            <Button
              onClick={() => setTelaAtiva("checkins")}
              className={telaAtiva === "checkins" ? style.botaoAtivo : ""}
            >
              <span className="d-flex">
                <FaCheck size={25} /> Checkins
              </span>
            </Button>

            <Button
              onClick={() => setTelaAtiva("mesas")}
              className={telaAtiva === "mesas" ? style.botaoAtivo : ""}
            >
              <span className="d-flex">
                <MdOutlineTableRestaurant size={25} /> Mesas
              </span>
            </Button>
          </Stack>
        </Offcanvas.Body>
      </Offcanvas>
    </>
  );
}

export default SideBar;
