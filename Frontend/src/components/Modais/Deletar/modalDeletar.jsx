import { useEffect, useState } from "react";
import { Button, Form, Modal, Stack } from "react-bootstrap";
import style from "./modalDeletar.module.css"

function ModalDeletar ({ handleClose, deletar, show }) {



  return (
    <Modal style={{zIndex: 1200}} show={show} onHide={handleClose}>
      <div className={style.modal}>

        <Modal.Header  closeButton>
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
            </div>
     
    </Modal>
  );
}

export default ModalDeletar