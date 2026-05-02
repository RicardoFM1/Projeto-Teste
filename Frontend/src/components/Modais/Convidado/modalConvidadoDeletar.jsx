import { useEffect, useState } from "react";
import { Button, Form, Modal, Stack } from "react-bootstrap";
import style from "./modalConvidado.module.css";

function ConvidadoModalDeletar ({ handleClose, deletar, show }) {



  return (
    <Modal style={{ zIndex: "10000" }} show={show} onHide={handleClose}>
     
        <Modal.Header closeButton>
          <Modal.Title>Tem certeza que deseja deletar?</Modal.Title>
        </Modal.Header>
        <Modal.Body>
          Essa ação é irreversível
        </Modal.Body>
        <Modal.Footer>
          <Button
            className="ignorar-fonte-btn"
            variant="secondary"
            onClick={handleClose}
          >
            Cancelar
          </Button>
          <Button onClick={deletar} variant="danger" className="ignorar-fonte-btn">
            Sim
          </Button>
        </Modal.Footer>
     
    </Modal>
  );
}

export default ConvidadoModalDeletar