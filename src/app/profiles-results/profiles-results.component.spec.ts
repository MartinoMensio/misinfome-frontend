import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ProfilesResultsComponent } from './profiles-results.component';

describe('ResultsComponent', () => {
  let component: ProfilesResultsComponent;
  let fixture: ComponentFixture<ProfilesResultsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ProfilesResultsComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(ProfilesResultsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
