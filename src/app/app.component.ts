import { Component } from '@angular/core';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ContactModalComponent } from './contact-modal/contact-modal.component';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  title = 'misinfome-frontend-v2';

  constructor(private modalService: NgbModal) {}

  open_contact() {
    const modalRef = this.modalService.open(ContactModalComponent);
  }
}
